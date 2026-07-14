<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set, $record) => $record
                        ? null
                        : $set('slug', Str::slug($state)))
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->helperText('Used internally to link checkouts and past subscriptions to this plan. Changing it after clients have subscribed is not recommended.'),
                TextInput::make('amount')
                    ->label('Price (₦/month)')
                    ->required()
                    ->numeric()
                    ->prefix('₦'),
                TextInput::make('message_limit')
                    ->label('Message limit / month')
                    ->numeric()
                    ->minValue(1)
                    ->placeholder('Unlimited')
                    ->helperText('Chat messages the widget will process per month on this plan. Leave blank for unlimited.'),
                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),
                Repeater::make('features')
                    ->simple(
                        TextInput::make('feature')->required()
                    )
                    ->addActionLabel('Add feature')
                    ->reorderable()
                    ->columnSpanFull(),
                Toggle::make('is_popular')
                    ->label('Highlight as "Most Popular"'),
                Toggle::make('is_active')
                    ->label('Visible to clients / on the website')
                    ->default(true),
            ])
            ->columns(2);
    }
}
