<?php

namespace App\Filament\Resources\KnowledgeBase\Schemas;

use App\Models\Client;
use App\Models\KnowledgeBaseEntry;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class KnowledgeBaseEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = (bool) auth()->user()?->is_admin;

        return $schema->components([
            Section::make('Entry Details')
                ->description('What the AI chat widget shows to visitors (FAQ) or draws on as background knowledge (Article).')
                ->schema([
                    $isAdmin
                        ? Select::make('client_id')
                            ->label('Client')
                            ->options(Client::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                        : Hidden::make('client_id')->default(auth()->user()?->client_id),
                    Select::make('type')
                        ->label('Type')
                        ->options(KnowledgeBaseEntry::TYPES)
                        ->default('faq')
                        ->required()
                        ->helperText(function (Get $get) {
                            $base = 'FAQ: shown as a tappable question in the widget\'s FAQ tab, answered instantly from here — the AI is never involved. Article: not shown directly to visitors, but given to the AI as background knowledge for any conversation.';

                            $client = Client::find($get('client_id'));

                            if (! $client) {
                                return $base;
                            }

                            $limit = $client->faqLimitForCurrentPlan();
                            $usage = $limit === null
                                ? "{$client->faqsUsedCount()} FAQs used, unlimited on this plan"
                                : "{$client->faqsUsedCount()} of {$limit} FAQs used on this plan";

                            return "{$base}\n\n{$usage}.";
                        }),
                    TextInput::make('title')
                        ->label('Title / Question')
                        ->required()
                        ->maxLength(200),
                    Textarea::make('content')
                        ->label('Content / Answer')
                        ->required()
                        ->rows(4)
                        ->maxLength(3000)
                        ->helperText('For a FAQ, this is shown to the visitor exactly as written — keep it clear and complete. For an Article, this is given to the AI as context.')
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Inactive entries are kept but never shown to visitors or sent to the AI.'),
                    TextInput::make('sort_order')
                        ->label('Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers show first. Leave as 0 if it doesn\'t matter.'),
                ])
                ->columns(2)
                ->columnSpan('full'),
        ]);
    }
}
