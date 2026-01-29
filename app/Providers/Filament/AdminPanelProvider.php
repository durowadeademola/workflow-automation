<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
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
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->brandName('Bluestrike')
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
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::styles.after',
                fn (): string => Blade::render('
                <style>
                /* 1. DESKTOP & GENERAL CARD STYLE */
                .fi-sidebar-nav {
                    margin: 1rem !important;
                    border-radius: 1.5rem !important;
                    background-color: white !important;
                    border: 1px solid rgba(0, 0, 0, 0.05) !important;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                    padding: 0.5rem !important;
                }
    
                .fi-layout {
                    background-color: #f8fafc !important;
                }
    
                aside.fi-sidebar {
                    background-color: transparent !important;
                    border: none !important;
                }
    
                /* 2. MOBILE SPECIFIC FIX */
                /* This ensures the slide-out drawer doesn\'t span too wide */
                @media (max-width: 1024px) {
                    .fi-sidebar {
                        /* This forces the drawer width to be smaller */
                        width: 16rem !important; 
                    }
    
                    /* This makes the navigation card fit perfectly inside the smaller drawer */
                    .fi-sidebar-nav {
                        margin: 0.5rem !important;
                        height: calc(100vh - 1rem) !important;
                    }
                    
                    /* This allows you to see the page content on the right side */
                    .fi-sidebar-close-overlay {
                        backdrop-filter: blur(2px);
                    }
                }
    
                /* Hide default collapse arrow */
                .fi-sidebar-collapse-button {
                    display: none !important;
                }
                </style>
            '),
            );
    }
}
