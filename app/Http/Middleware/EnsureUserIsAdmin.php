<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin-only gate for routes outside Filament (Filament pages gate themselves via canAccess()).
 * The `web` guard is shared by both panels, so being logged in isn't enough — a client or agent
 * session must not reach admin-only endpoints like Workflow Studio's API.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_admin, 403);

        return $next($request);
    }
}
