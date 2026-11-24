<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Both panels' login page — only the greeting differs from Filament's own. Lives outside
 * app/Filament/Pages so the panels' discoverPages() doesn't also register it as a dashboard page.
 */
class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return 'Welcome back';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return 'Enter your details to log in.';
    }
}
