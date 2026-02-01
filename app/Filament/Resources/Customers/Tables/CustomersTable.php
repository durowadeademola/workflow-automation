<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Filament\Exports\CustomerExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(CustomerExporter::class),
            ])
            ->columns([
                // TextColumn::make('client.name')
                //     ->label('Client')
                //     ->placeholder('—')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('chat_id')
                    ->label('Customer id')
                    ->searchable(),
                // BadgeColumn::make('state')
                //     ->colors([
                //         'warning' => 'AWAITING_PRODUCT',
                //         'warning' => 'AWAITING_SPECS',
                //         'success' => 'DONE',
                //     ])
                //     ->searchable(),
                // TextColumn::make('agent.name')
                //     ->label('Agent')
                //     ->placeholder('—')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('item.name')
                //     ->label('Product')
                //     ->placeholder('—')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('message'),
                TextColumn::make('platform'),
                // TextColumn::make('product')
                //     ->searchable(),
                // TextColumn::make('specs')
                //     ->label('Specs')
                //     ->searchable(),
                // TextColumn::make('assigned_agent')
                //     ->label('Assigned Agent')
                //     ->searchable(),
                // TextColumn::make('agent_email')
                //     ->label('Agent Email')
                //     ->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'OPEN',
                        'warning' => 'ASSIGNED',
                        'success' => 'CLOSED',
                    ])
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit order')
                    ->modalWidth('lg') // Keeps the modal small and clean
                    ->form([
                        Select::make('status')
                            ->options([
                                'OPEN' => 'OPEN',
                                'ASSIGNED' => 'ASSIGNED',
                                'CLOSED' => 'CLOSED',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('platform')->options([
                            'Telegram' => 'Telegram',
                            'WhatsApp' => 'WhatsApp',
                        ])
                            ->default('telegram'),
                    ]),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
