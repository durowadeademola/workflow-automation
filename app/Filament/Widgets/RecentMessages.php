<?php

namespace App\Filament\Widgets;

use App\Filament\Exports\MessageExporter;
use App\Models\Message;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentMessages extends TableWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user && $user->is_client;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(MessageExporter::class),
            ])
            ->query(fn (): Builder => Message::query()
                ->with('customer')
                ->where('client_id', auth()->user()?->client_id)
            // Use select to get the latest message per customer
                ->select('messages.*')
                ->whereIn('id', function ($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('messages')
                        ->groupBy('customer_id'); // Groups the list by user
                })
                ->latest())
            ->columns([
                TextColumn::make('customer.display_name')
                    ->label('Name')
                    ->searchable(query: fn ($query, $search) => $query->orWhereHas(
                        'customer',
                        fn ($q) => $q->where('name', 'like', "%{$search}%"),
                    )),

                TextColumn::make('content')
                    ->label('Message')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Telegram' => 'primary',
                        'WhatsApp' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('viewHistory')
                    ->label('Chat history')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->modalHeading(fn (Message $record) => 'Chat with '.($record->customer?->display_name ?? 'User'))
                    ->modalSubmitAction(false) // Hide the save button since it's read-only
                    ->modalWidth('xl')
                    ->schema(function (Message $record) {
                        // Fetch all messages between this customer and this client
                        $history = Message::where('customer_id', $record->customer_id)
                            ->where('client_id', $record->client_id)
                            ->orderBy('created_at', 'desc')
                            ->get();

                        return [
                            \Filament\Forms\Components\Placeholder::make('history')
                                ->label('')
                                ->content(view('filament.components.chat-history', ['messages' => $history])),
                        ];
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
