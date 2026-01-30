<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->placeholder('—')
                    ->searchable()
                    //->visible(fn () => auth()->user()->is_admin) // Only Admin sees this
                    ->sortable(),
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
                    //->visible(fn () => auth()->user()->is_admin),// Only Admin sees this
                IconColumn::make('is_client')
                    ->label('Client')
                    ->boolean(),
                IconColumn::make('is_agent')
                    ->label('Agent')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
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
