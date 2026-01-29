<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('client_id')
                    ->default(auth()->user()->client_id),
                TextInput::make('name')
                    ->placeholder('Enter agent name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->placeholder('Enter agent email')
                    ->email()
                    ->required(),
                TextInput::make('telephone')
                    ->placeholder('Enter agent telephone')
                    ->numeric(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]);
    }
}
