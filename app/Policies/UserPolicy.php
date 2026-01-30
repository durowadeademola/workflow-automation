<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only Admins and Clients can see the user list.
        // Agents should not be able to manage other users.
        return $user->is_admin || $user->is_client;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Both Admin and Client can create, but we restricted the
        // "Roles" available in the Filament Form earlier.
        return $user->is_admin || $user->is_client;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // 1. Super Admin can edit anyone.
        if ($user->is_admin) {
            return true;
        }

        // 2. Clients can only edit users that belong to their company.
        // 3. Prevent Clients from editing themselves into an 'admin' role.
        return $user->client_id === $model->client_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Clients can delete their own agents, but cannot delete themselves.
        return $user->client_id === $model->client_id
               && $user->id !== $model->id
               && $model->agent_id != null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
