<?php

namespace App\Filament\Pages;

use App\Models\Client;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class WidgetSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PaintBrush;

    protected static ?string $navigationLabel = 'Widget Settings';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.widget-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (bool) $user && $user->is_client;
    }

    public function getClient(): ?Client
    {
        return Auth::user()->client;
    }

    public function mount(): void
    {
        if ($client = $this->getClient()) {
            $this->form->fill(array_merge(
                Client::WIDGET_DEFAULTS,
                array_filter(
                    $client->only(array_keys(Client::WIDGET_DEFAULTS)),
                    fn ($value) => $value !== null,
                ),
            ));
        }
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('widget_agent_name')
                    ->label('Assistant name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Shown in the widget header and next to its replies.'),
                ColorPicker::make('widget_primary_color')
                    ->label('Brand color'),
                Textarea::make('widget_greeting')
                    ->label('Greeting message')
                    ->rows(2)
                    ->maxLength(500)
                    ->helperText('The first message visitors see when they open the widget.'),
                Textarea::make('widget_system_prompt')
                    ->label('AI instructions')
                    ->rows(4)
                    ->maxLength(2000)
                    ->helperText('Tells the AI how to behave — tone, what it should and shouldn\'t answer, etc.'),
                TextInput::make('widget_wa_number')
                    ->label('WhatsApp number')
                    ->tel()
                    ->maxLength(50)
                    ->helperText('Digits only with country code, e.g. 2348012345678 — used for the "Chat on WhatsApp" link.'),
                Select::make('widget_position')
                    ->label('Widget position')
                    ->options([
                        'right' => 'Bottom right',
                        'left' => 'Bottom left',
                    ]),
                Repeater::make('widget_quick_replies')
                    ->label('Quick reply suggestions')
                    ->simple(
                        TextInput::make('reply')->required()->maxLength(80),
                    )
                    ->reorderable()
                    ->addActionLabel('Add quick reply')
                    ->helperText('Short buttons shown under the greeting, e.g. "Pricing", "Book a call".'),
                TextInput::make('widget_auto_open_delay')
                    ->label('Auto-open delay')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('ms')
                    ->helperText('How long after the page loads before the widget pops open on its own.'),
            ]);
    }

    public function save(): void
    {
        $client = $this->getClient();

        if (! $client) {
            Notification::make()->title('Your account is not linked to a business.')->danger()->send();

            return;
        }

        $client->update($this->form->getState());

        Notification::make()->title('Widget settings saved.')->success()->send();
    }

    public function getEmbedSnippet(): ?string
    {
        return $this->getClient()?->getWidgetEmbedSnippet();
    }
}
