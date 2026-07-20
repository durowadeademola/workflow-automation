<?php

namespace App\Filament\Resources\Messages\Tables;

use App\Filament\Resources\Messages\MessageResource;
use App\Models\Message;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.display_name')
                    ->label('Customer ID / Name')
                    ->getStateUsing(fn (Message $record) => $record->customer?->name
                        ?: ($record->customer ? "{$record->source} Visitor #{$record->customer->id}" : null))
                    ->placeholder('—')
                    ->searchable(query: fn ($query, $search) => $query->orWhereHas(
                        'customer',
                        fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('username', 'like', "%{$search}%"),
                    )),
                TextColumn::make('content')
                    ->label('Last message')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Telegram' => 'primary',
                        'WhatsApp' => 'success',
                        'Website' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->getStateUsing(fn (Message $record) => Message::where('customer_id', $record->customer_id)->count())
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->label('Last activity')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('source')
                    ->options([
                        'Telegram' => 'Telegram',
                        'WhatsApp' => 'WhatsApp',
                        'Website' => 'Website',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordUrl(fn (Message $record) => MessageResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('viewConversation')
                    ->label('View conversation')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn (Message $record) => MessageResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
