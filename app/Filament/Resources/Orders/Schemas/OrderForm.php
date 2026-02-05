<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Management')
                    ->description('Manage customer orders')
                    ->schema([
                        Hidden::make('client_id')
                            ->default(auth()->user()->client_id),
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'username')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->username ?? "Customer #{$record->id}")
                            ->preload(),
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? "Product #{$record->id}")
                            ->preload(),
                        // Select::make('service_id')
                        //     ->label('Service')
                        //     ->relationship('service', 'name')
                        //     ->preload(),
                        TextInput::make('customer_name'),
                        TextInput::make('customer_phone')
                            ->tel(),
                        TextInput::make('customer_email')
                            ->email()
                            ->required(),
                        TextInput::make('order_reference'),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        Select::make('currency')
                            ->options([
                                'NGN' => 'NGN',
                                'USD' => 'USD',
                                'GBP' => 'GBP',
                                'EUR' => 'EUR',
                            ])
                            ->default('NGN'),
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'pending_payment' => 'Pending Payment',
                                'paid' => 'Paid',
                                'processing' => 'Processing',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->default('new'),
                        TextInput::make('source'),
                        // TextInput::make('items'),
                        Textarea::make('notes')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
