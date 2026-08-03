<?php

namespace App\Filament\Resources\Marketing\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSent = fn ($record) => $record && $record->status !== 'draft';

        return $schema->components([
            Section::make('Newsletter')
                ->schema([
                    Select::make('client_id')
                        ->label('Send as')
                        ->options(fn () => ['' => 'Blueflow (agency newsletter)'] + Client::query()
                            ->whereJsonContains('features', 'marketing-automation')
                            ->pluck('name', 'id')
                            ->all())
                        ->native(false)
                        ->visible(fn () => (bool) auth()->user()?->is_admin)
                        ->helperText('Only admins choose this — a client always sends to their own contacts.'),
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->disabled($isSent),
                    Textarea::make('body_html')
                        ->label('Message')
                        ->required()
                        ->rows(14)
                        ->disabled($isSent)
                        ->helperText('Basic HTML is fine. Use {{name}} anywhere to insert each recipient\'s name.')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                // Without this the Section only claims one half of the
                // page's outer 2-column grid (the same grid
                // MarketingJourneyForm fills by placing two Sections side by
                // side) — a single Section otherwise sits in a page-width
                // box with the whole right half empty.
                ->columnSpanFull(),
        ]);
    }
}
