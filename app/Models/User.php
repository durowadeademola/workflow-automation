<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
// use Filament\Models\Contracts\HasDatabaseNotifications;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'agent_id',
        'name',
        'email',
        'password',
        'is_admin',
        'is_client',
        'is_agent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Laravel's default relies on a `password.reset` named route, which
     * doesn't exist here — every login is through the Filament panel, whose
     * reset route is namespaced under the panel id. See ResetPasswordNotification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = Filament::getResetPasswordUrl($token, $this);

        $this->notify(new ResetPasswordNotification($url));
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if ($this->is_admin) {
            return true;
        }

        // Clients (and their agents) are locked out until an admin approves
        // the business — self-registration creates them with status
        // "pending", and "inactive" covers a business suspended afterward.
        if ($this->is_client || $this->is_agent) {
            return $this->client?->status === 'active';
        }

        return false;
    }
}
