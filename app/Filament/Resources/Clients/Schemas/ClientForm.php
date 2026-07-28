<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Enter client full name or business name')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->placeholder('Enter client email')
                            ->unique(ignoreRecord: true),
                        TextInput::make('telephone')
                            ->placeholder('Enter client telephone number'),
                        Select::make('type')
                            ->options([
                                'internal' => 'Internal',
                                'commercial-bank' => 'Commercial Bank',
                                'ecommerce' => 'Ecommerce',
                                'fintech' => 'Fintech',
                                'food-beverage' => 'Food & Beverage',
                                'government' => 'Government',
                                'healthcare' => 'Healthcare',
                                'tech' => 'Technology',
                                'law' => 'Law',
                                'logistics' => 'Logistics',
                                'microfinance' => 'Microfinance Bank',
                                'online-store' => 'Online Store',
                                'real-estate' => 'Real Estate',
                                'school' => 'School',
                                'sme' => 'SME',
                                'others' => 'Others',
                            ])
                            ->searchable(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending approval',
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'rejected' => 'Rejected',
                            ])
                            ->helperText('Clients can\'t log in until this is "Active" — self-registered businesses start as "Pending approval".')
                            ->default('active'),
                        Textarea::make('rejection_reason')
                            ->label('Rejection reason')
                            ->rows(2)
                            ->visible(fn ($record) => $record?->status === 'rejected')
                            ->columnSpanFull(),
                        Toggle::make('bypass_plan_limits')
                            ->label('Unrestricted (bypass plan limits)')
                            ->helperText('Ignores subscription status and message/appointment/lead limits entirely for this client — the widget, appointments, and lead capture all behave as if always subscribed with unlimited quota. Use for internal/demo/testing accounts only, not real customers.')
                            ->columnSpanFull(),
                        CheckboxList::make('features')
                            ->label('Services / dashboard access')
                            ->options(Client::FEATURES)
                            ->helperText('Which dashboard menus this client (and their agents) can see — e.g. no "Chat Widget" means no Widget Settings or Live Chat. Blank/unset means unrestricted (legacy clients).')
                            ->columns(2)
                            ->columnSpanFull(),
                        Select::make('workflow_engine')
                            ->label('Workflow engine')
                            ->options([
                                'n8n' => 'n8n (webhook)',
                                'native' => 'Native (Blueflow Engine)',
                            ])
                            ->default('n8n')
                            ->required()
                            ->helperText('Which engine actually processes this client\'s chat messages. Switching to "Native" runs everything in-process via the "chat-widget-reply" AutomationWorkflow instead of the n8n webhook below — see Workflow Studio to inspect or edit it.')
                            ->live()
                            ->columnSpanFull()
                            ->visible(fn (?Client $record) => $record?->hasFeature('chat-widget')),
                        TextInput::make('webhook_url')
                            ->label('n8n Webhook URL')
                            ->helperText('Where the chat widget\'s messages are forwarded once this client has an active subscription. Only used while Workflow engine is set to "n8n" above.')
                            ->url()
                            ->required(fn (Get $get) => $get('workflow_engine') !== 'native')
                            ->columnSpanFull()
                            ->visible(fn (?Client $record, Get $get) => $record?->hasFeature('chat-widget') && $get('workflow_engine') !== 'native'),
                    ])->columns(2)
                    ->columnSpan('full'),

                Section::make('Widget Configuration')
                    ->description('What this client has configured from their dashboard. These exact values are sent to whichever chat engine is selected above (n8n webhook or the native workflow) with every message — use them to build or debug that client\'s workflow.')
                    ->schema([
                        Toggle::make('widget_ready')
                            ->label('Ready to go live')
                            ->helperText('Prefer the "Mark Widget Ready" button on the Clients list instead — it notifies the client automatically. Toggling it here just corrects the flag without sending anything.')
                            ->columnSpanFull(),
                        DateTimePicker::make('widget_ready_at')
                            ->label('Marked ready at')
                            ->disabled()
                            ->columnSpanFull(),
                        TextInput::make('widget_agent_name')
                            ->label('Assistant name')
                            ->maxLength(255)
                            ->placeholder('AI Assistant'),
                        ColorPicker::make('widget_primary_color')
                            ->label('Brand color'),
                        TextInput::make('widget_wa_number')
                            ->label('WhatsApp number')
                            ->tel()
                            ->maxLength(50),
                        Select::make('widget_position')
                            ->label('Widget position')
                            ->options([
                                'right' => 'Bottom right',
                                'left' => 'Bottom left',
                            ]),
                        TextInput::make('widget_auto_open_delay')
                            ->label('Auto-open delay')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('ms')
                            ->helperText('How long after the page loads before the widget pops open on its own.'),
                        Textarea::make('widget_greeting')
                            ->label('Greeting message')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Textarea::make('widget_system_prompt')
                            ->label('AI instructions (sent to the chat engine as systemPrompt)')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Repeater::make('widget_quick_replies')
                            ->label('Quick reply suggestions')
                            ->simple(
                                TextInput::make('reply')->maxLength(80),
                            )
                            ->reorderable()
                            ->addActionLabel('Add quick reply')
                            ->columnSpanFull(),
                        Textarea::make('widget_embed_snippet')
                            ->label('Embed code currently in use')
                            ->default(fn ($record) => $record?->getWidgetEmbedSnippet())
                            ->rows(11)
                            ->disabled()
                            ->dehydrated(false)
                            ->extraInputAttributes(['class' => 'font-mono !text-xs'])
                            ->helperText('Read-only — this reflects what the client has saved from their own dashboard.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->visible(fn (?Client $record) => $record?->hasFeature('chat-widget')),
            ]);
    }
}
