<?php

namespace App\Filament\Resources\AgentProfiles\Tables;

use App\Models\Client;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AgentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service')
                    ->label('Used for')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (Client::FEATURES[$state] ?? $state) : 'Platform-wide')
                    ->color(fn (?string $state) => $state ? 'info' : 'gray'),
                TextColumn::make('model')
                    ->label('Model / provider')
                    ->placeholder('—')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('In use')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->label('Used for')
                    ->options(Client::FEATURES),
                TernaryFilter::make('is_active')
                    ->label('In use'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
