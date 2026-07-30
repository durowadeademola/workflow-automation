<?php

namespace App\Filament\Pages;

use App\Models\Client;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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

        return (bool) $user && $user->is_client && $user->client?->hasFeature('chat-widget');
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
                    // Matches Client::getWidgetConfig()'s own filter — an
                    // empty array (e.g. a client who cleared their quick
                    // replies without adding new ones) falls back to the
                    // default here too, so this form never shows something
                    // different from what the live widget actually renders.
                    fn ($value) => $value !== null && $value !== [],
                ),
                // Deliberately excluded from the fallback above: the three
                // baseline quick replies are always appended live by
                // Client::getWidgetConfig(), never stored on the client
                // itself, so this field shows only what the client has
                // actually added — never the defaults, which aren't real,
                // editable/removable rows here.
                ['widget_quick_replies' => $client->widget_quick_replies ?? []],
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
                    ->helperText('Short buttons shown under the greeting, e.g. "Pricing", "Book a call". "Book an appointment", "Chat with a staff member", and "Register with us" always show too, after whatever you add here.'),
                TextInput::make('widget_auto_open_delay')
                    ->label('Auto-open delay')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('ms')
                    ->helperText('How long after the page loads before the widget pops open on its own.'),

                Section::make('Working hours')
                    ->description('Controls when the AI is allowed to hand a visitor off to a human agent, and lets the AI itself correctly answer "are you open?" questions. Outside these hours, it tells visitors the team is offline and points them to WhatsApp instead.')
                    ->schema([
                        Toggle::make('working_hours_enabled')
                            ->label('Restrict agent handoff to working hours')
                            ->live()
                            ->helperText('Off by default — the AI can hand off to an agent any time.'),
                        Select::make('timezone')
                            ->label('Timezone')
                            ->options(array_combine(
                                \DateTimeZone::listIdentifiers(),
                                \DateTimeZone::listIdentifiers(),
                            ))
                            ->searchable()
                            ->visible(fn ($get) => $get('working_hours_enabled')),
                        CheckboxList::make('working_days')
                            ->label('Working days')
                            ->options(Client::WORKING_DAYS)
                            ->columns(4)
                            ->visible(fn ($get) => $get('working_hours_enabled')),
                        TimePicker::make('working_hours_start')
                            ->label('Start time')
                            ->seconds(false)
                            ->visible(fn ($get) => $get('working_hours_enabled')),
                        TimePicker::make('working_hours_end')
                            ->label('End time')
                            ->seconds(false)
                            ->visible(fn ($get) => $get('working_hours_enabled')),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
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

    public function isWidgetReady(): bool
    {
        return (bool) $this->getClient()?->widget_ready;
    }

    public function isWidgetEnabled(): bool
    {
        return (bool) $this->getClient()?->widget_enabled;
    }

    /**
     * Instant on/off — unlike the customization form above, this doesn't
     * wait for a "Save Changes" click, since a client toggling this off
     * (e.g. outside business hours) wants it to take effect immediately.
     */
    public function toggleWidgetEnabled(): void
    {
        $client = $this->getClient();

        if (! $client) {
            return;
        }

        $client->update(['widget_enabled' => ! $client->widget_enabled]);

        Notification::make()
            ->title($client->widget_enabled ? 'Widget turned on' : 'Widget turned off')
            ->body($client->widget_enabled
                ? 'Your assistant is answering visitors again.'
                : 'Your assistant will not respond to visitors until you turn it back on.')
            ->success()
            ->send();
    }
}
