<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('client_id')
                    ->default(auth()->user()?->client_id),
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'username')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->username ?? $record->name ?? "Customer #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('source')
                    ->options([
                        'Telegram' => 'Telegram',
                        'WhatsApp' => 'WhatsApp',
                        'Website' => 'Website',
                    ])
                    ->required(),
                Toggle::make('from_customer')
                    ->label('Sent by customer')
                    ->default(true),
                Textarea::make('content')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
