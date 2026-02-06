<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Enter program full name'),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active'),
                    ])->columns(2)
                    ->columnSpan('full'),
                // TextInput::make('client_id')
                //     ->numeric(),
                // TextInput::make('name'),
                // Textarea::make('description')
                //     ->columnSpanFull(),
                // TextInput::make('status'),
            ]);
    }
}
