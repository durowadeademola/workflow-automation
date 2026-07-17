<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Review;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = auth()->user()?->is_admin;

        return $schema
            ->components([
                Section::make('Your Review')
                    ->description('Tell us about your experience — with your permission, we may feature this on our website.')
                    ->schema([
                        Select::make('rating')
                            ->options([
                                5 => str_repeat('★', 5).' Excellent',
                                4 => str_repeat('★', 4).str_repeat('☆', 1).' Good',
                                3 => str_repeat('★', 3).str_repeat('☆', 2).' Average',
                                2 => str_repeat('★', 2).str_repeat('☆', 3).' Poor',
                                1 => str_repeat('★', 1).str_repeat('☆', 4).' Very poor',
                            ])
                            ->required()
                            ->native(false),
                        Textarea::make('description')
                            ->label('Your experience')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => auth()->user()?->name),
                        TextInput::make('job_title')
                            ->label('Job title')
                            ->maxLength(255)
                            ->placeholder('e.g. Founder, Operations Manager'),
                        TextInput::make('company')
                            ->maxLength(255)
                            ->default(fn () => auth()->user()?->client?->name),
                        TextInput::make('location')
                            ->maxLength(255)
                            ->placeholder('e.g. Lagos'),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                Section::make('Moderation')
                    ->visible($isAdmin)
                    ->schema([
                        Select::make('status')
                            ->options(Review::STATUSES)
                            ->required()
                            ->native(false),
                        Select::make('is_featured')
                            ->label('Featured on homepage?')
                            ->options([1 => 'Yes', 0 => 'No'])
                            ->default(0)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
