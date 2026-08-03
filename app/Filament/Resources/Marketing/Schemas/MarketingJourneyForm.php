<?php

namespace App\Filament\Resources\Marketing\Schemas;

use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MarketingJourneyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Journey Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),
                    Select::make('trigger_event')
                        ->label('Starts when...')
                        ->options([
                            '' => 'Manual only — enroll a segment yourself',
                            'appointment_booked' => 'A customer books an appointment',
                            'abandoned_booking' => "A customer registers interest but doesn't book",
                            're_engagement' => 'A customer goes quiet (inactivity)',
                        ])
                        ->default('')
                        ->live()
                        ->helperText('Manual journeys only start when you use the "Enroll a segment" action.'),
                    TextInput::make('trigger_config.hours')
                        ->label('Hours before considered "abandoned"')
                        ->numeric()
                        ->default(24)
                        ->visible(fn (Get $get) => $get('trigger_event') === 'abandoned_booking'),
                    TextInput::make('trigger_config.days')
                        ->label('Days of inactivity before re-engaging')
                        ->numeric()
                        ->default(30)
                        ->visible(fn (Get $get) => $get('trigger_event') === 're_engagement'),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Inactive journeys never enroll anyone and are skipped by the scheduler.'),
                ])
                ->columns(2),

            Section::make('Steps')
                ->description('Each step waits the given amount of time after the previous one completes before it runs.')
                ->schema([
                    Repeater::make('steps')
                        ->relationship()
                        ->orderColumn('order')
                        ->schema([
                            Select::make('channel')
                                ->label('Channel')
                                ->options([
                                    'email' => 'Email — live',
                                    //'whatsapp' => 'WhatsApp — needs setup',
                                    //ms' => 'SMS — needs setup',
                                   //telegram' => 'Telegram — needs setup',
                                ])
                                ->default('email')
                                ->required()
                                ->live(),
                            TextInput::make('config.subject')
                                ->label('Subject')
                                ->visible(fn (Get $get) => $get('channel') === 'email')
                                ->required(fn (Get $get) => $get('channel') === 'email')
                                ->maxLength(200),
                            Textarea::make('config.body')
                                ->label('Message')
                                ->rows(4)
                                ->required()
                                ->helperText('Merge fields: {{trigger.customer.name}}, {{trigger.customer.leadIntent}}, {{trigger.latestAppointment.scheduledAt}}, {{trigger.client.name}}.')
                                ->columnSpanFull(),
                            TextInput::make('wait_amount')
                                ->label('Wait before this step')
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            Select::make('wait_unit')
                                ->label('Unit')
                                ->options(['minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days'])
                                ->default('hours')
                                ->required(),
                            Select::make('exit_condition_preset')
                                ->label('Stop the journey here if...')
                                ->options([
                                    '' => 'Never — always continue',
                                    'appointment_booked' => 'Customer has since booked an appointment',
                                    'unsubscribed' => 'Customer has unsubscribed',
                                ])
                                ->default('')
                                ->columnSpanFull(),
                        ])
                        ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::withGeneratedKeyAndType($data))
                        ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::withGeneratedKeyAndType($data))
                        ->itemLabel(fn (array $state): string => $state['config']['subject'] ?? ucfirst($state['channel'] ?? 'step'))
                        ->addActionLabel('Add step')
                        ->reorderable()
                        ->collapsible()
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /**
     * `key` and `type` are internal (StepRegistry lookup / cross-step
     * addressing) — the form only exposes the friendlier `channel` select,
     * so both are derived here rather than shown as fields a client could
     * get wrong.
     */
    private static function withGeneratedKeyAndType(array $data): array
    {
        $data['key'] ??= 'step-'.Str::random(8);
        $data['type'] = match ($data['channel'] ?? 'email') {
            'whatsapp' => 'send_whatsapp',
            'sms' => 'send_sms',
            'telegram' => 'send_telegram',
            default => 'send_email',
        };

        return $data;
    }
}
