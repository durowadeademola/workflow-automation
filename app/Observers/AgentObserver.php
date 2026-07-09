<?php

namespace App\Observers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AgentObserver
{
    /**
     * Handle the Agent "created" event.
     */
    public function created(Agent $agent): void
    {
        // Check if a user with this email already exists to avoid crashes
        $existingUser = User::firstWhere('email', $agent->email);

        if (! $existingUser) {
            $this->createAgentUser($agent);
        }
    }

    /**
     * Handle the Agent "updated" event.
     */
    public function updated(Agent $agent): void
    {
        // Optional: Keep the user's name/email in sync if the agent is updated
        $user = User::where('email', $agent->getOriginal('email'))
            ->orWhere('email', $agent->email)
            ->first();
        if ($user) {
            $user->update([
                'name' => $agent->name,
                'email' => $agent->email,
            ]);
        } else {
            $this->createAgentUser($agent);
        }
    }

    /**
     * Create a login for a new agent with a random password and email them a link to set their own.
     */
    private function createAgentUser(Agent $agent): void
    {
        $user = User::create([
            'name' => $agent->name,
            'email' => $agent->email,
            'agent_id' => $agent->id,
            'client_id' => $agent->client_id,
            'password' => bcrypt(Str::random(32)),
            'is_agent' => true,
            'is_client' => false,
            'is_admin' => false,
        ]);

        Password::sendResetLink(['email' => $user->email]);
    }

    /**
     * Handle the Agent "deleted" event.
     */
    public function deleted(Agent $agent): void
    {
        // Optional: Keep the user's name/email in sync if the agent is deleted
        $user = User::where('email', $agent->getOriginal('email'))
            ->orWhere('email', $agent->email)
            ->first();
        if ($user) {
            $user->delete();
        }
    }

    /**
     * Handle the Agent "restored" event.
     */
    public function restored(Agent $agent): void
    {
        //
    }

    /**
     * Handle the Agent "force deleted" event.
     */
    public function forceDeleted(Agent $agent): void
    {
        //
    }
}
