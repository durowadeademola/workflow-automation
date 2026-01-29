<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('client_id')
                    ->default(auth()->user()->client_id),
                Select::make('agent_id')
                    ->label('Agent')
                    ->relationship('agent', 'name')
                    ->preload()
                    ->required(),
                Select::make('item_id')
                    ->label('Product')
                    ->relationship('item', 'name')
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->placeholder('Enter customer full name or business name'),
                TextInput::make('username')
                    ->placeholder('Enter customer username'),
                TextInput::make('chat_id')
                    ->placeholder('Enter customer chat id')
                    ->numeric(),
                Select::make('state')->options([
                    'DONE' => 'DONE',
                    'AWAITING_PRODUCT' => 'AWAITING PRODUCT',
                    'AWAITING_SPECS' => 'AWAITING SPECS',
                ]),
                TextInput::make('message')
                    ->placeholder('Enter customer message'),
                Select::make('platform')->options([
                    'Telegram' => 'Telegram',
                    'WhatsApp' => 'WhatsApp',
                ])
                    ->default('telegram'),
                TextInput::make('product')
                    ->placeholder('Enter customer product'),
                TextInput::make('specs')
                    ->placeholder('Enter customer specs'),
                TextInput::make('assigned_agent')
                    ->placeholder('Enter the assigned agent'),
                TextInput::make('agent_email')
                    ->placeholder('Enter agent email'),
                Select::make('status')->options([
                    'OPEN' => 'OPEN',
                    'ASSIGNED' => 'ASSIGNED',
                    'CLOSED' => 'CLOSED',
                ])
                    ->default('OPEN'),
            ]);
    }
}
