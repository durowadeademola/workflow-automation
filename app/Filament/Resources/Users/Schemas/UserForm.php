<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->placeholder('Enter user full name or business name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->placeholder('Enter user personal or business email')
                    ->email()
                    ->required(),
                // DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(fn ($record) => $record === null)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

                Toggle::make('is_admin')
                    ->label('Admin'),
                Toggle::make('is_client')
                    ->label('Client'),
                Toggle::make('is_agent')
                    ->label('Agent'),
            ]);
    }
}
