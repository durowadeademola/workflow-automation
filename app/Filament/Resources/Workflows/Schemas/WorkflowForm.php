<?php

namespace App\Filament\Resources\Workflows\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkflowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Workflow Management')
                    ->description('Manage available client workflows')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->preload(),
                        TextInput::make('name')
                            ->placeholder('Enter product name')
                            ->required(),
                        Textarea::make('description')
                            ->placeholder('Enter product description')
                            ->columnSpanFull(),
                        TextInput::make('platform')
                            ->placeholder('Enter platform name'),
                        Toggle::make('is_published')
                            ->label('Publish'),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
