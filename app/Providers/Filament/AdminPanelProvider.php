<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->databaseNotifications()
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->favicon(asset('favicon-32x32.png'))
            ->login()
            ->passwordReset()
            ->profile()
            // Optional, per-user — isRequired defaults to false, so login
            // isn't blocked for anyone who hasn't set this up. A user turns
            // it on/off themselves from Settings (see Settings.php), and
            // only gets challenged for a code at login once they have.
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
                EmailAuthentication::make(),
            ])
            ->userMenuItems([
                // The default logout action renders as a real <form
                // method="post"> (via ->url()->postToUrl()) that submits
                // immediately on click — requiresConfirmation() alone
                // doesn't stop that, since Action::toHtml() only renders a
                // Livewire-wired <button> (the thing that can actually open
                // a modal) when there's no URL at all. Clearing both forces
                // it to render as a button and go through mountAction(), so
                // the actual logout has to be replicated in ->action()
                // instead of relying on the URL.
                'logout' => fn (Action $action) => $action
                    ->color('danger')
                    ->url(null)
                    ->postToUrl(false)
                    ->requiresConfirmation()
                    ->modalHeading('Log out?')
                    ->modalDescription('You\'ll need to sign in again to get back in.')
                    ->modalSubmitActionLabel('Log out')
                    ->action(function () {
                        Filament::auth()->logout();
                        request()->session()->invalidate();
                        request()->session()->regenerateToken();

                        return redirect(Filament::getLoginUrl());
                    }),
            ])
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->brandName('Blueflow')
            ->widgets([])
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authGuard('web')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
