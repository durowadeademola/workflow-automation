<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
// use Filament\Models\Contracts\HasDatabaseNotifications;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasEmailAuthentication, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, InteractsWithEmailAuthentication, MustVerifyEmailTrait, Notifiable, SoftDeletes;

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
        'is_active',
        'email_notifications_enabled',
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
            'is_active' => 'boolean',
            'email_notifications_enabled' => 'boolean',
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

    /**
     * Same reasoning as sendPasswordResetNotification() above: Laravel's own
     * MustVerifyEmail trait builds its link via route('verification.verify',
     * ...), which doesn't exist here — every login is through the Filament
     * panel, whose verify route is namespaced under the panel id instead.
     *
     * Deliberately resolves the 'user' panel by id rather than calling
     * Filament::getVerifyEmailUrl($this) — that helper builds the URL for
     * whichever panel the CURRENT request happens to be in, which is wrong
     * here: this fires from ClientObserver/UserObserver, often while an
     * admin (in the /admin panel) is the one approving/creating the
     * account, not the client/agent it's actually for. Only clients/agents
     * ever go through email verification at all (see UserPanelProvider),
     * so the 'user' panel is always the right one regardless of who/what
     * triggered this.
     */
    public function sendEmailVerificationNotification(): void
    {
        $notification = app(VerifyEmail::class);
        $notification->url = Filament::getPanel('user')->getVerifyEmailUrl($this);

        $this->notify($notification);
    }

    /**
     * Admins and everyone else use separate panels (/admin vs /user) with no
     * overlap — an admin can't log into /user and a client/agent can't log
     * into /admin, even though both panels discover the exact same
     * resource/page classes (each one's own canViewAny()/canAccess() still
     * gates by role on top of this).
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Deactivated by an admin — blocked from either panel regardless of role.
        if (! $this->is_active) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return (bool) $this->is_admin;
        }

        if ($panel->getId() === 'user') {
            if (! ($this->is_client || $this->is_agent)) {
                return false;
            }

            // Clients (and their agents) are locked out until an admin
            // approves the business — self-registration creates them with
            // status "pending", and "inactive"/"rejected" cover the other
            // ways a business ends up unable to log in.
            return $this->client?->status === 'active';
        }

        return false;
    }
}
