<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 200;

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) Auth::user();
    }

    public function getUser(): User
    {
        return Auth::user();
    }

    public function mount(): void
    {
        $this->form->fill([
            'name' => $this->getUser()->name,
            'email' => $this->getUser()->email,
            'email_notifications_enabled' => $this->getUser()->email_notifications_enabled,
            'features' => $this->clientFeaturesForForm($this->getUser()->client),
        ]);
    }

    /**
     * The CheckboxList only ever offers Client::SELF_REGISTRATION_FEATURES
     * as options, so its dehydrated state must be scoped to that same
     * subset — Filament validates submitted CheckboxList values against
     * getEnabledOptions() only, so anything outside it (e.g. an
     * admin-granted "coming soon" feature) would fail validation and block
     * the whole Settings save. `null` on the client means "unrestricted"
     * (see Client::hasFeature), so it's treated as having every
     * self-registration feature already enabled.
     */
    private function clientFeaturesForForm(?Client $client): array
    {
        if (! $client) {
            return [];
        }

        if ($client->features === null) {
            return Client::SELF_REGISTRATION_FEATURES;
        }

        return array_values(array_intersect($client->features, Client::SELF_REGISTRATION_FEATURES));
    }

    /**
     * Features the client has that aren't self-service manageable here —
     * shown read-only so they're not left wondering where a service went.
     */
    private function otherClientFeatures(?Client $client): array
    {
        if (! $client) {
            return [];
        }

        $features = $client->features ?? array_keys(Client::FEATURES);

        return collect(array_diff($features, Client::SELF_REGISTRATION_FEATURES))
            ->map(fn (string $slug) => Client::FEATURES[$slug] ?? $slug)
            ->values()
            ->all();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    /**
     * A separate schema (not part of the main form/save() flow) so each
     * provider's own setup/disable actions — each with their own modal and
     * state — never get tangled up with $this->form->getState() or the
     * "Save Changes" button above. Loops over whatever's actually
     * registered on the panel (AdminPanelProvider/UserPanelProvider) rather
     * than hardcoding provider ids, so its config (recoverable(),
     * codeExpiryMinutes(), etc.) can never drift from what's actually used
     * to verify codes at login, and any future provider added to the panel
     * shows up here automatically.
     */
    public function twoFactorAuthenticationSchema(Schema $schema): Schema
    {
        return $schema->components(
            collect(Filament::getMultiFactorAuthenticationProviders())
                ->map(fn ($provider) => Group::make($provider->getManagementSchemaComponents())->statePath($provider->getId()))
                ->values()
                ->all(),
        );
    }

    public function form(Schema $schema): Schema
    {
        $user = $this->getUser();

        return $schema
            ->components([
                Section::make('Profile')
                    ->description('Your name and the email address you use to log in.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'users', column: 'email', ignorable: $user),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                Section::make('Change Password')
                    ->description('Leave these blank to keep your current password.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current password')
                            ->password()
                            ->revealable()
                            ->autocomplete('current-password'),
                        TextInput::make('new_password')
                            ->label('New password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->rule(Password::default())
                            ->confirmed(),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm new password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                Section::make('Notifications')
                    ->description('Control which alerts land in your inbox.')
                    ->schema([
                        Toggle::make('email_notifications_enabled')
                            ->label('Email notifications')
                            ->helperText('Alerts like new appointments, handoff requests, and billing updates. You\'ll still see everything in your notification bell either way — this only controls email.')
                            ->default(true),
                    ])
                    ->columnSpan('full'),

                // Business-level, so only the client owner manages it — same
                // rule WidgetSettings already uses for widget config, not
                // something an agent should be changing on the business's
                // behalf.
                Section::make('Services')
                    ->description('Add a service to unlock its dashboard pages. Only what\'s actually available today can be toggled here — the rest is coming soon.')
                    ->visible(fn () => (bool) $user->is_client)
                    ->schema([
                        CheckboxList::make('features')
                            ->label('')
                            ->options(array_intersect_key(Client::FEATURES, array_flip(Client::SELF_REGISTRATION_FEATURES)))
                            ->helperText('Removing a service you\'re actively subscribed to isn\'t allowed here — cancel that subscription from Billing first.')
                            ->columns(2)
                            ->bulkToggleable(false),
                        Placeholder::make('other_features')
                            ->label('Also enabled on your account')
                            ->visible(fn () => filled($this->otherClientFeatures($user->client)))
                            ->content(fn () => implode(', ', $this->otherClientFeatures($user->client))),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public function save(): void
    {
        $user = $this->getUser();
        $state = $this->form->getState();

        if (filled($state['new_password'] ?? null)) {
            if (! filled($state['current_password'] ?? null) || ! Hash::check($state['current_password'], $user->password)) {
                Notification::make()
                    ->title('Current password is incorrect.')
                    ->danger()
                    ->send();

                return;
            }

            $user->password = $state['new_password'];
        }

        $user->name = $state['name'];
        $user->email = $state['email'];
        $user->email_notifications_enabled = (bool) ($state['email_notifications_enabled'] ?? true);
        $user->save();

        $blockedRemovals = [];

        if ($user->is_client && ($client = $user->client)) {
            $blockedRemovals = $this->saveFeatures($client, $state['features'] ?? []);
        }

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'email_notifications_enabled' => $user->email_notifications_enabled,
            'features' => $this->clientFeaturesForForm($user->client),
        ]);

        if ($blockedRemovals) {
            Notification::make()
                ->title('Settings saved, but not everything')
                ->body("Couldn't remove ".implode(', ', $blockedRemovals).' — cancel the active subscription for that service first.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Settings saved.')
            ->success()
            ->send();
    }

    /**
     * Applies the client's new self-service feature selection, refusing to
     * remove any service that still has an active or in-progress
     * subscription tied to it. The CheckboxList only ever offers
     * SELF_REGISTRATION_FEATURES, so anything else the client has (admin
     * granted) is preserved untouched here rather than round-tripped
     * through form state.
     *
     * @param  array<int, string>  $requestedSelfRegFeatures
     * @return array<int, string> service labels that couldn't be removed
     */
    private function saveFeatures(Client $client, array $requestedSelfRegFeatures): array
    {
        $otherFeatures = $client->features === null
            ? array_diff(array_keys(Client::FEATURES), Client::SELF_REGISTRATION_FEATURES)
            : array_diff($client->features, Client::SELF_REGISTRATION_FEATURES);

        $currentSelfReg = $this->clientFeaturesForForm($client);
        $removed = array_diff($currentSelfReg, $requestedSelfRegFeatures);

        $blocked = [];
        $finalSelfReg = $requestedSelfRegFeatures;

        foreach ($removed as $service) {
            if ($client->hasActiveOrPendingSubscriptionForService($service)) {
                $blocked[] = Client::FEATURES[$service] ?? $service;
                $finalSelfReg[] = $service;
            }
        }

        $client->update(['features' => array_values(array_unique(array_merge($otherFeatures, $finalSelfReg)))]);

        return $blocked;
    }
}
