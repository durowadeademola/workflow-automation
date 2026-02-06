<?php

namespace App\Filament\Resources\Domains\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DomainForm
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
                        Select::make('parent_id')
                            ->label('Domain')
                            ->relationship('parent', 'url')
                            ->preload(),
                        Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->preload(),
                        TextInput::make('url')
                            ->required()
                            ->placeholder('Enter domain url'),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'scanning' => 'Scanning',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                            ])
                            ->default('pending'),
                        Toggle::make('is_subdomain')
                            ->required(),
                    ])->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
