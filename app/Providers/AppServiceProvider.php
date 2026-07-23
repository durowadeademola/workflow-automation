<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mime\Part\DataPart;

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
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\Lead::observe(\App\Observers\LeadObserver::class);
        \App\Models\WidgetConversation::observe(\App\Observers\WidgetConversationObserver::class);
        \App\Models\Client::observe(\App\Observers\ClientObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // The branded mail template's logo (resources/views/vendor/mail)
        // is embedded as a real inline attachment under a fixed Content-ID
        // (matching the "cid:blueflow-logo@blueflow" src hardcoded in
        // message.blade.php) rather than a remote asset() URL, since that
        // URL would point at whatever APP_URL happens to be — often a
        // local-only dev domain unreachable from a real recipient's mail
        // client — and would just show as a broken image for anyone
        // outside this machine.
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $html = $event->message->getHtmlBody();

            if ($html && str_contains($html, 'cid:blueflow-logo@blueflow')) {
                $part = DataPart::fromPath(public_path('favicon-192x192.png'), 'blueflow-logo.png')
                    ->asInline()
                    ->setContentId('blueflow-logo@blueflow');

                $event->message->addPart($part);
            }
        });
    }
}
