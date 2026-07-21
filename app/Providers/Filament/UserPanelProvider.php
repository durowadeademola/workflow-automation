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
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Clients and agents log in here, under /user — kept as a separate panel
 * from /admin (AdminPanelProvider) so admins and everyone else never share
 * a login surface. Resources/pages are discovered from the exact same
 * directories as the admin panel; each one's own canViewAny()/canAccess()
 * already gates by is_admin/is_client/is_agent, and canAccessPanel() below
 * additionally stops a client or agent from ever authenticating into
 * /admin, and an admin from ever authenticating into /user, so nothing
 * meant for the other role is actually reachable despite the shared
 * discovery paths.
 */
class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->databaseNotifications()
            ->id('user')
            ->path('user')
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
            // Same "BA" badge as the marketing site's navbar logo, so the
            // login screen doesn't feel like a completely separate product.
            // Linked back to the marketing homepage — the standard way an
            // auth screen offers a way back out, without adding a full nav.
            ->brandLogo(new HtmlString(
                '<a href="/"><div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center"><span class="text-white font-bold text-sm">BA</span></div></a>'
            ))
            ->brandLogoHeight('2rem')
            ->widgets([])
            ->darkMode()
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
