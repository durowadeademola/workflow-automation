<?php

namespace App\Filament\Widgets;

use App\Models\Message;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentMessages extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        /** * We check if the user exists, is a client, and if their
         * associated customer profile has the right type.
         */
        return $user && $user->is_client
            && in_array(strtolower($user->client?->type), [
                'online-store',
                'real-estate',
                'logistics',
                'sme',
                'ecommerce',
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Message::query()
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
                TextColumn::make('customer.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('customer.username')
                    ->label('Username')
                    ->searchable(),

                // TextColumn::make('content')
                //     ->label('Message')
                //     ->limit(50)
                //     ->searchable(),

                TextColumn::make('source')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\Action::make('viewHistory')
                    ->label('View Chat')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->modalHeading(fn (Message $record) => 'Chat with '.($record->customer?->username ?? 'User'))
                    ->modalSubmitAction(false) // Hide the save button since it's read-only
                    ->modalWidth('xl')
                    ->form(function (Message $record) {
                        // Fetch all messages between this customer and this client
                        $history = Message::where('customer_id', $record->customer_id)
                            ->where('client_id', $record->client_id)
                            ->oldest()
                            ->get();

                        return [
                            \Filament\Forms\Components\Placeholder::make('history')
                                ->label('')
                                ->content(view('filament.components.chat-history', ['messages' => $history])),
                        ];
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
