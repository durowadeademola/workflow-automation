<?php

namespace App\Filament\Resources\Plans\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plan Details')
                    ->description('Configure pricing, limits, and features for this plan')
                    ->schema([
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
                        Select::make('service')
                            ->label('Restricted to service')
                            ->options(Client::FEATURES)
                            ->placeholder('Universal — visible to every client')
                            ->helperText('Leave blank for a plan every client can see. Set a service to only show this plan to clients who selected it (e.g. a WhatsApp-only plan for WhatsApp Automation clients).'),
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
                        TextInput::make('appointment_limit')
                            ->label('Appointment limit / month')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Unlimited')
                            ->helperText('Appointments the AI (or the client themselves) can book per billing period on this plan. Leave blank for unlimited.'),
                        TextInput::make('lead_limit')
                            ->label('Lead qualification limit / month')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Unlimited')
                            ->helperText('Visitors the AI can flag as a qualified lead per billing period on this plan. Leave blank for unlimited.'),
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
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
