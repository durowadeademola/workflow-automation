<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate::policy(User::class, UserPolicy::class);
        \App\Models\Agent::observe(\App\Observers\AgentObserver::class);
        //\App\Models\Order::observe(\App\Observers\OrderObserver::class);
        //\App\Models\Customer::observe(\App\Observers\OrderObserver::class);
    }
}
