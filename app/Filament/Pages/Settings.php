<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
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

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'email_notifications_enabled' => $user->email_notifications_enabled,
        ]);

        Notification::make()
            ->title('Settings saved.')
            ->success()
            ->send();
    }
}
