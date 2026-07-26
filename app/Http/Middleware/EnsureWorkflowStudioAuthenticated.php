<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Its own session flag, not Laravel's Auth facade / `web` guard — this area
 * is deliberately not tied to the `users` table or Filament's session at
 * all, so neither can be used to reach the other.
 */
class EnsureWorkflowStudioAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('workflow_studio_authenticated')) {
            return redirect()->route('workflow-studio.login');
        }

        return $next($request);
    }
}
