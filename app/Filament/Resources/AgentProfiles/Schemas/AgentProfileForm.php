<?php

namespace App\Filament\Resources\AgentProfiles\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AgentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('AI Agent Details')
                    ->description('Track which AI models power each service — record-keeping only, this doesn\'t affect behavior.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Agent name')
                            ->placeholder('e.g. Website Chat Assistant')
                            ->required()
                            ->maxLength(255),
                        Select::make('service')
                            ->label('Used for')
                            ->options(Client::FEATURES)
                            ->placeholder('Platform-wide — not tied to one service'),
                        TextInput::make('model')
                            ->label('Model / provider')
                            ->placeholder('e.g. Groq Llama 3.3, GPT-4o')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('In use')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
