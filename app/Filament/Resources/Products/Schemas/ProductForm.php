<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Hidden::make('client_id')
                            ->default(auth()->user()->client_id),
                        TextInput::make('name')
                            ->placeholder('Enter product name')
                            ->required(),
                        Textarea::make('description')
                            ->placeholder('Enter product description')
                            ->columnSpanFull(),
                        TextInput::make('price')
                            ->placeholder('Enter product price')
                            ->numeric()
                            ->prefix('₦'),
                        TextInput::make('quantity')
                            ->placeholder('Enter product available quantity')
                            ->numeric(),
                        Select::make('currency')
                            ->options([
                                'NGN' => 'NGN',
                                'USD' => 'USD',
                                'GBP' => 'GBP',
                                'EUR' => 'EUR',
                            ])
                            ->default('NGN'),
                        Toggle::make('is_available')
                            ->label('Available'),
                        FileUpload::make('image_path')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('products')
                            ->image(),
                    ])->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
