<?php

namespace App\Filament\Resources\Messages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
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
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.username')
                    ->label('Username')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('content')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Telegram' => 'primary',
                        'WhatsApp' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('from_customer')
                    ->label('From Customer')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Sent At')
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
