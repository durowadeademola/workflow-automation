<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Filament\Tables\Columns\PersonColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                PersonColumn::make('name', fn ($record) => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),
                TextColumn::make('business_name')
                    ->label('Business')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('phone')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('interest')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('source')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),
                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'new',
                        'warning' => 'contacted',
                        'success' => 'qualified',
                        'gray' => 'closed',
                    ]),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'closed' => 'Closed',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
