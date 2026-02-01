<?php

namespace App\Filament\Widgets;

use App\Filament\Exports\OrderExporter;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrders extends TableWidget
{
    protected static ?string $heading = 'Assigned Orders';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        /** * We check if the user exists, is a client, and if their
         * associated customer profile has the right type.
         */
        return $user && $user->is_agent
            && in_array(strtolower($user->client?->type), [
                'online-store',
                'real-estate',
                'logistics',
                'sme',
                'ecommerce',
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(OrderExporter::class),
            ])
            ->query(fn (): Builder => Order::query()
                ->where('client_id', auth()->user()?->client_id)
                ->where('agent_id', auth()->user()?->agent_id))
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

                TextColumn::make('notes')
                    ->label('Description')
                    ->searchable(),

                // TextColumn::make('agent.name')
                //     ->label('Agent')
                //     ->placeholder('—')
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('source')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

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
                TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                // TextColumn::make('currency')
                //     ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color('success'),
                TextColumn::make('created_at')
                    ->label('Ordered at')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
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
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Update Description'),
                    ]),
                // DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
