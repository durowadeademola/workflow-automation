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
        return $user
            && $user->is_client
            && $user->client?->type === 'online-store';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Message::query()
                ->where('client_id', auth()->user()?->client_id)
                ->latest()
                ->limit(20))
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('content')
                    ->label('Message')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('source')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
