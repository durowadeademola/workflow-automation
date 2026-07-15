<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Client;
use App\Models\Plan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription Details')
                    ->description('Billing period, plan, and status for this client\'s subscription')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->options(fn () => Client::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('plan_id')
                            ->label('Plan')
                            ->options(fn () => Plan::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($plan = Plan::find($state)) {
                                    $set('plan', $plan->slug);
                                    $set('name', $plan->name);
                                    $set('amount', $plan->amount);
                                }
                            })
                            ->helperText('Picking a plan fills in the fields below — they\'re editable in case this record needs to differ from the live plan (e.g. a historical or custom-priced subscription).'),
                        TextInput::make('plan')
                            ->label('Plan slug (snapshot)')
                            ->required(),
                        TextInput::make('name')
                            ->label('Plan name (snapshot)'),
                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('₦'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                        Toggle::make('is_active'),
                        TextInput::make('paystack_reference')
                            ->label('Paystack Reference')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
