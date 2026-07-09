<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.n8n.webhook_secret');

        if (! $expected) {
            abort(500, 'Webhook secret is not configured.');
        }

        $provided = $request->header('X-Webhook-Secret');

        if (! $provided || ! hash_equals($expected, $provided)) {
            abort(401, 'Invalid webhook secret.');
        }

        return $next($request);
    }
}
