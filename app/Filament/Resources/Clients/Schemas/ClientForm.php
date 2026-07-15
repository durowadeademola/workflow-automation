<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                            ])
                            ->helperText('Clients can\'t log in until this is "Active" — self-registered businesses start as "Pending approval".')
                            ->default('active'),
                        CheckboxList::make('features')
                            ->label('Services / dashboard access')
                            ->options(Client::FEATURES)
                            ->helperText('Which dashboard menus this client (and their agents) can see — e.g. no "Chat Widget" means no Widget Settings or Live Chat. Blank/unset means unrestricted (legacy clients).')
                            ->columns(2)
                            ->columnSpanFull(),
                        TextInput::make('webhook_url')
                            ->label('n8n Webhook URL')
                            ->helperText('Where the chat widget\'s messages are forwarded once this client has an active subscription.')
                            ->url()
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->columnSpan('full'),

                Section::make('Widget Configuration')
                    ->description('What this client has configured from their dashboard. These exact values are sent to the n8n webhook above with every message — use them to build or debug that client\'s workflow.')
                    ->schema([
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
                        Textarea::make('widget_greeting')
                            ->label('Greeting message')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Textarea::make('widget_system_prompt')
                            ->label('AI instructions (sent to n8n as systemPrompt)')
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
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
