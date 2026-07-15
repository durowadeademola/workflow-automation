<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lead Details')
                    ->description('Contact form submissions and inquiries')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('business_name'),
                        TextInput::make('email')
                            ->email()
                            ->required(),
                        TextInput::make('phone'),
                        TextInput::make('interest'),
                        Textarea::make('message')
                            ->columnSpanFull(),
                        TextInput::make('source'),
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'qualified' => 'Qualified',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
