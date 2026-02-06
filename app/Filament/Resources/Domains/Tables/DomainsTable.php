<?php

namespace App\Filament\Resources\Domains\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DomainsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('program.name')
                    ->label('Program')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'info' => 'pending',
                        'primary' => 'scanning',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->searchable(),
                IconColumn::make('is_subdomain')
                    ->label('Sudomain')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    // RestoreBulkAction::make(),
                ]),
            ]);
    }
}
