<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->placeholder('Enter client full name or business name')
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->placeholder('Enter client email')
                    ->unique(ignoreRecord: true),
                TextInput::make('telephone')
                    ->placeholder('Enter client telephone number'),
                Select::make('type')
                    ->options([
                        'bank' => 'Bank',
                        'fintech' => 'Fintech',
                        'hospital' => 'Hospital',
                        'logistics' => 'Logistics',
                        'microfinance' => 'Microfinance Bank',
                        'online-store' => 'Online Store',
                        'real-estate' => 'Real Estate',
                        'school' => 'School',
                        'sme' => 'SME',
                        'others' => 'Others',
                    ]),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]);
    }
}
