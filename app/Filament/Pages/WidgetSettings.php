<?php

namespace App\Filament\Pages;

use App\Models\AutomationWorkflow;
use App\Models\Client;
use App\Workflow\WorkflowExecutor;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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

    protected static string|UnitEnum|null $navigationGroup = 'Chat Widget';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.widget-settings';

    public ?array $data = [];

    public ?array $lastCrawlSummary = null;

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
                // Not part of WIDGET_DEFAULTS above — these configure the
                // RAG crawler, not the live widget's own appearance/behavior.
                [
                    'website_url' => $client->website_url,
                    'crawl_paths' => $client->crawl_paths ?: ['/'],
                ],
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

                Section::make('Website content')
                    ->description('Which pages the AI reads to answer questions about your business. Save your changes here, then use "Recrawl my site" below to (re)index them.')
                    ->schema([
                        TextInput::make('website_url')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://yourbusiness.com')
                            ->helperText('Your site\'s base address, with no trailing slash.'),
                        TagsInput::make('crawl_paths')
                            ->label('Pages to crawl')
                            ->placeholder('/about')
                            ->helperText('Paths on your site to read, e.g. /, /about, /pricing. Defaults to just the homepage (/).'),
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

    public function canRecrawl(): bool
    {
        return (bool) $this->getClient()?->usesNativeWorkflowEngine();
    }

    public function recrawlAction(): Action
    {
        return Action::make('recrawl')
            ->label('Recrawl my site')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Recrawl your website?')
            ->modalDescription('Re-reads every page below and replaces what the AI currently knows about your site with the fresh content. This can take a little while for several pages — you can keep using the dashboard while it runs.')
            ->modalSubmitActionLabel('Recrawl now')
            ->action(fn () => $this->recrawl());
    }

    /**
     * Auto-opened from recrawl() (via mountAction) only when at least one
     * page didn't cleanly index — a plain toast can't fit a per-page
     * breakdown, and burying the reason in storage/logs/laravel.log means
     * the client never actually finds out something needs attention.
     */
    public function recrawlResultsAction(): Action
    {
        return Action::make('recrawlResults')
            ->label('Recrawl results')
            ->modalHeading('Recrawl results')
            ->modalDescription('Some pages had a problem — here\'s what happened for each one.')
            ->modalContent(fn () => view('filament.pages.partials.crawl-results', [
                'pageResults' => $this->lastCrawlSummary['pageResults'] ?? [],
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    /**
     * Runs the "website-crawler" workflow in-process against this client's
     * own website_url/crawl_paths — the same AutomationWorkflow the native
     * engine already had seeded (parameterized via {{trigger.clientId}} /
     * {{trigger.websiteUrl}} / {{trigger.paths}}), just never actually
     * invoked by anything until now. Runs synchronously rather than via the
     * workflow's own /trigger webhook — this is already an authenticated
     * in-app action, so there's no reason to round-trip through HTTP.
     */
    public function recrawl(): void
    {
        $client = $this->getClient();

        if (! $client) {
            Notification::make()->title('Your account is not linked to a business.')->danger()->send();

            return;
        }

        if (blank($client->website_url)) {
            Notification::make()->title('Add your website URL above first, then save.')->danger()->send();

            return;
        }

        $workflow = AutomationWorkflow::where('slug', 'website-crawler')->first();

        if (! $workflow) {
            Notification::make()->title('Recrawl is not available right now — please try again shortly.')->danger()->send();

            return;
        }

        $run = app(WorkflowExecutor::class)->run($workflow, [
            'clientId' => $client->id,
            'websiteUrl' => $client->website_url,
            'paths' => $client->crawl_paths ?: ['/'],
        ]);

        if ($run->status !== 'completed') {
            Notification::make()
                ->title('Recrawl failed')
                ->body($run->error ?: 'Something went wrong — please try again shortly.')
                ->danger()
                ->send();

            return;
        }

        $summary = $run->context['steps']['summary'] ?? [];
        $pageResults = $summary['pageResults'] ?? [];
        $hasIssues = collect($pageResults)->contains(fn ($page) => $page['status'] !== 'indexed');

        Notification::make()
            ->title('Recrawl complete')
            ->body(sprintf(
                '%d page(s) read, %d chunk(s) indexed%s.',
                $summary['pagesFetched'] ?? 0,
                $summary['chunksStored'] ?? 0,
                ($summary['chunksFailed'] ?? 0) > 0 ? ', '.$summary['chunksFailed'].' failed' : ''
            ))
            ->color($hasIssues ? 'warning' : 'success')
            ->send();

        // A toast alone doesn't explain WHICH page had a problem or WHY —
        // exactly the gap that made a real fetch failure invisible until
        // someone went digging through logs. Only pops up when something's
        // actually worth a second look, so a clean recrawl stays a single
        // unobtrusive toast.
        //
        // Dispatched as a browser event rather than calling mountAction()
        // directly here: this method is itself already running inside
        // Filament's own mountAction('recrawl') request-handling (that's
        // how the button triggers it), and nesting a second mountAction()
        // call inside that gets silently dropped once the outer action's
        // response finishes — confirmed by reproducing the real click flow
        // in a browser, not just calling recrawl() directly in a test.
        // Dispatching an event lets the browser open the results modal in
        // its own separate request, after the first one has fully closed.
        if ($hasIssues) {
            $this->lastCrawlSummary = $summary;
            $this->dispatch('recrawl-finished');
        }
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
