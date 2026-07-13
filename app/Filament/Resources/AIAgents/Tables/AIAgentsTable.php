<?php

namespace App\Filament\Resources\AIAgents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AIAgentsTable
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

                TextColumn::make('model')
                    ->label('AI Model')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('prompt')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->prompt)
                    ->wrap(),

                BadgeColumn::make('success')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Success' : 'Failed')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),

                TextColumn::make('latency')
                    ->label('Latency (ms)')
                    ->suffix(' ms')
                    ->sortable(),

                IconColumn::make('error')
                    ->label('Error')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('model')
                    ->options([
                        'groq-beta' => 'Groq Beta',
                        'groq-vision' => 'Groq Vision',
                    ]),
                TernaryFilter::make('success')
                    ->label('Request Status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
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
