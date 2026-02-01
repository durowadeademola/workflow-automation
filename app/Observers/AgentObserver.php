<?php

namespace App\Observers;

use App\Models\Agent;
use App\Models\User;

class AgentObserver
{
    /**
     * Handle the Agent "created" event.
     */
    public function created(Agent $agent): void
    {
        // $rawPassword = strtoupper(Str::random(7));
        // Check if a user with this email already exists to avoid crashes
        $existingUser = User::firstWhere('email', $agent->email);

        if (! $existingUser) {
            $user = User::create([
                'name' => $agent->name,
                'email' => $agent->email,
                'agent_id' => $agent->id,
                'client_id' => $agent->client_id,
                'password' => bcrypt('password'), // Default password
                'is_agent' => true,
                'is_client' => false,
                'is_admin' => false,
            ]);

            // If you have a user_id column on your agents table, link it now
            // $agent->update(['user_id' => $user->id]);
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
            $user = User::create([
                'name' => $agent->name,
                'email' => $agent->email,
                'agent_id' => $agent->id,
                'client_id' => $agent->client_id,
                'password' => bcrypt('password'),
                'is_agent' => true,
                'is_client' => false,
                'is_admin' => false,
            ]);
        }
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
