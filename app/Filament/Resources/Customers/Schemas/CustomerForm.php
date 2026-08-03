<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Management')
                    ->description('Manage leads and customers')
                    ->schema([
                        Hidden::make('client_id')
                            ->default(auth()->user()?->client_id),
                        Hidden::make('status')
                            ->default('OPEN'),
                        TextInput::make('name')
                            ->label('Customer name')
                            ->placeholder('Enter customer full name or business name')
                            // Anonymous visitors never give a name, so the raw
                            // column is null — show their generated display
                            // code instead of leaving the field looking empty.
                            ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                if (blank($state) && $record) {
                                    $component->state($record->display_name);
                                }
                            }),
                        TextInput::make('email')
                            ->email()
                            ->placeholder('Enter customer email'),
                        TextInput::make('phone')
                            ->label('Telephone')
                            ->tel()
                            ->placeholder('Enter customer telephone'),
                        Select::make('platform')->options([
                            'Telegram' => 'Telegram',
                            'WhatsApp' => 'WhatsApp',
                            'Website' => 'Website',
                        ]),
                    ])->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
