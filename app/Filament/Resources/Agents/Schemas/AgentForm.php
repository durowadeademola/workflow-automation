<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_id')
                    ->numeric(),
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
