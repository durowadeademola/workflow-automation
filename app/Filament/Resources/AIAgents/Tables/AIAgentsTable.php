<?php

namespace App\Filament\Resources\AIAgents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AIAgentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                        'grok-beta' => 'Grok Beta',
                        'grok-vision' => 'Grok Vision',
                    ]),

                TernaryFilter::make('success')
                    ->label('Request Status'),
                // TextColumn::make('customers.name')->label('Customer'),
                // TextColumn::make('metadata.priority')
                //     ->label('Priority')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'Hot' => 'danger',
                //         'Warm' => 'warning',
                //         'Cold' => 'info',
                //         default => 'gray',
                //     }),
                // IconColumn::make('success')->boolean(),
                // TextColumn::make('created_at')->dateTime(),
                // TextColumn::make('customer_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('client_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('order_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('product_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('service_id')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('source')
                //     ->searchable(),
                // TextColumn::make('model')
                //     ->searchable(),
                // IconColumn::make('success')
                //     ->boolean(),
                // TextColumn::make('latency')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    // RestoreBulkAction::make(),
                ]),
            ]);
    }
}
