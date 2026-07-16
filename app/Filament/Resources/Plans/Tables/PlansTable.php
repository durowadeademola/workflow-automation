<?php

namespace App\Filament\Resources\Plans\Tables;

use App\Models\Client;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('service')
                    ->label('Service')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (Client::FEATURES[$state] ?? $state) : 'Universal')
                    ->color(fn (?string $state) => $state ? 'info' : 'gray'),
                TextColumn::make('amount')
                    ->label('Price')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('message_limit')
                    ->label('Msg limit/mo')
                    ->placeholder('Unlimited')
                    ->numeric(),
                TextColumn::make('appointment_limit')
                    ->label('Appt limit/mo')
                    ->placeholder('Unlimited')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('lead_limit')
                    ->label('Leads limit/mo')
                    ->placeholder('Unlimited')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('subscriptions_count')
                    ->label('Subscribers')
                    ->counts('subscriptions'),
                IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('service')
                    ->options(Client::FEATURES)
                    ->label('Service'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
