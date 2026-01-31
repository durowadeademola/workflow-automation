<?php

namespace App\Filament\Resources\AIAgents\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AIAgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('source')
                //     ->disabled(),

                // TextInput::make('latency')
                //     ->suffix(' ms')
                //     ->disabled(),

                // TextInput::make('success')
                //     ->formatStateUsing(fn ($state) => $state ? 'Success' : 'Failed')
                //     ->disabled(),

                // Textarea::make('prompt')
                //     ->label('Prompt')
                //     ->rows(6)
                //     ->disabled(),

                // Textarea::make('response')
                //     ->label('AI Response')
                //     ->rows(10)
                //     ->disabled(),

                // Textarea::make('error')
                //     ->label('Error Message')
                //     ->visible(fn ($record) => ! $record?->success)
                //     ->disabled(),

                Section::make('Lead Context')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customers', 'name')
                            ->searchable(),
                        TextInput::make('source')->disabled(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                Section::make('AI Analysis')
                    ->schema([
                        TextInput::make('model')->disabled(),
                        Textarea::make('prompt')
                            ->rows(3)
                            ->disabled(),
                        Textarea::make('response')
                            ->rows(5)
                            ->label('AI Insights')
                            ->disabled()
                            ->columnSpan('full'),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                Section::make('Performance & Metadata')
                    ->schema([
                        Toggle::make('success'),
                        TextInput::make('latency')->numeric()->suffix('ms')->disabled(),
                        KeyValue::make('metadata')->disabled(),
                    ])
                    ->columns(3)
                    ->columnSpan('full'),
            ]);
    }
}
