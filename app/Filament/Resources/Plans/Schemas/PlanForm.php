<?php

namespace App\Filament\Resources\Plans\Schemas;

use App\Models\Client;
use Filament\Forms\Components\DateTimePicker;
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
                        TextInput::make('promo_price')
                            ->label('Promo price (₦/month)')
                            ->numeric()
                            ->prefix('₦')
                            ->placeholder('No promotion running')
                            ->helperText('Set a lower price to run a promotion — clients see the regular price struck through, this price, and the percentage saved. This is what actually gets charged, not the regular price above.'),
                        DateTimePicker::make('promo_ends_at')
                            ->label('Promo ends at')
                            ->native(false)
                            ->helperText('Leave blank to run the promotion until you manually clear the promo price above.'),
                        TextInput::make('yearly_discount_percent')
                            ->label('Yearly billing discount')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%')
                            ->placeholder('No discount')
                            ->helperText('A standing discount for paying for a whole year up front — clients see 12x the monthly price struck through, and this discounted total next to it, same as a promotion. Leave blank to still offer yearly billing at the full 12x price with no discount shown.'),
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
                        TextInput::make('faq_limit')
                            ->label('FAQ limit')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Unlimited')
                            ->helperText('Total FAQ entries a client on this plan can have at once — not per month, since FAQs answer instantly with no AI call involved. Leave blank for unlimited.'),
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
