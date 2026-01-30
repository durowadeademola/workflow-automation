<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Management')
                    ->description('Manage user credentials and access levels')
                    ->schema([
                        // 1. CLIENT SELECTION
                        // Only Admin can pick a client. Clients are locked to their own ID.
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->preload(),
                            //->required(),
                           // ->visible(fn () => auth()->user()->is_admin) // Only Admin sees this
                           // ->default(auth()->user()->client_id),

                        // Hidden field for non-admins to ensure client_id is saved
                        // Hidden::make('client_id')
                        //     ->default(auth()->user()->client_id)
                        //     ->disabled(fn () => auth()->user()->is_admin),

                        // 2. USER DETAILS
                        TextInput::make('name')
                            ->placeholder('Enter full name')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null),

                        // 3. ROLE LOGIC (Replaces the 3 Toggles)
                        // We use a Select or segmented button to ensure ONLY ONE role is picked
                        Select::make('role_type')
                            ->label('Account Type')
                        // We don't save this to DB, so we must manually fetch the value for existing records
                            ->formatStateUsing(function ($record) {
                                // if (! $record) {
                                //     return 'is_agent';
                                // }
                                if ($record?->is_admin) {
                                    return 'is_admin';
                                }
                                if ($record?->is_client) {
                                    return 'is_client';
                                }

                                //return 'is_agent';
                            })
                            ->options(function () {
                                if (auth()->user()->is_admin) {
                                    return [
                                        'is_admin' => 'Admin',
                                        'is_client' => 'Client',
                                        //'is_agent' => 'Agent',
                                    ];
                                }

                               return [];
                            })
                            ->required()
                            ->live()
                            ->dehydrated(false) // CRITICAL: This prevents the "column not found" error
                            ->afterStateUpdated(function ($state, $set) {
                                // Reset all database toggles
                                $set('is_admin', false);
                                $set('is_client', false);
                                $set('is_agent', false);

                                // Set the correct one based on the selection
                                $set($state, true);
                            }),

                        // Add these Hidden fields so Filament actually saves the data to your columns
                        Hidden::make('is_admin'),
                        Hidden::make('is_client'),
                        //Hidden::make('is_agent'),

                    ])->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
