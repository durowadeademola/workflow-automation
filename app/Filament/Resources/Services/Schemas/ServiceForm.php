<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Hidden::make('client_id')
                            ->default(auth()->user()->client_id),
                        TextInput::make('name')
                            ->placeholder('Enter service name'),
                        Textarea::make('description')
                            ->placeholder('Enter service description')
                            ->columnSpanFull(),
                        TextInput::make('price')
                            ->placeholder('Enter service price')
                            ->numeric()
                            ->prefix('₦'),
                        Select::make('currency')
                            ->options([
                                'NGN' => 'NGN',
                                'USD' => 'USD',
                                'GBP' => 'GBP',
                                'EUR' => 'EUR',
                            ])
                            ->default('NGN'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required(),
                    ])->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
