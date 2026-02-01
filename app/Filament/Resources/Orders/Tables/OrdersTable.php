<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.username')
                    ->label('Customer')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('service.name')
                //     ->label('service')
                //     ->placeholder('—')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('customer_name')
                //     ->searchable(),
                // TextColumn::make('customer_phone')
                //     ->searchable(),
                // TextColumn::make('customer_email')
                //     ->searchable(),
                TextColumn::make('order_reference')
                    ->label('Order id')
                    ->searchable(),
                TextColumn::make('source')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('notes')
                    ->label('Description')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                // TextColumn::make('currency')
                //     ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color('success'),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit order')
                    ->modalWidth('lg') // Keeps the modal small and clean
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'pending_payment' => 'Pending Payment',
                                'paid' => 'Paid',
                                'processing' => 'Processing',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Description')
                            ->rows(3),
                    ]),
                // DeleteAction::make(),
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
