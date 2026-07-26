<?php

namespace App\Http\Controllers\WorkflowStudio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class WorkflowStudioAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->get('workflow_studio_authenticated')) {
            return redirect()->route('workflow-studio.app');
        }

        return view('workflow-studio.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $key = 'workflow-studio-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['username' => "Too many attempts. Try again in {$seconds} seconds."]);
        }

        $expectedUsername = config('workflow_studio.username');
        $expectedHash = config('workflow_studio.password_hash');

        $usernameMatches = $expectedUsername && hash_equals($expectedUsername, $validated['username']);
        $passwordMatches = $expectedHash && Hash::check($validated['password'], $expectedHash);

        if (! $usernameMatches || ! $passwordMatches) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['username' => 'Invalid credentials.']);
        }

        RateLimiter::clear($key);

        // Regenerated so a session id captured before login can't be reused
        // to inherit an authenticated session after the fact.
        $request->session()->regenerate();
        $request->session()->put('workflow_studio_authenticated', true);

        return redirect()->route('workflow-studio.app');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('workflow_studio_authenticated');
        $request->session()->regenerate();

        return redirect()->route('workflow-studio.login');
    }
}
