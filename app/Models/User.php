<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'api_token',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
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
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the WhatsApp connections for the user.
     */
    public function whatsappConnections()
    {
        return $this->hasMany(WhatsAppConnection::class);
    }

    /**
     * Get the WhatsApp groups for the user.
     */
    public function whatsappGroups()
    {
        return $this->hasMany(WhatsAppGroup::class);
    }

    /**
     * Get the extracted contacts for the user.
     */
    public function extractedContacts()
    {
        return $this->hasMany(ExtractedContact::class);
    }

    /**
     * Get the mass sendings for the user.
     */
    public function massSendings()
    {
        return $this->hasMany(MassSending::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get the subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for the user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Check if the user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Check if the user has an active subscription for a specific plan.
     */
    public function hasActiveSubscriptionForPlan(int $planId): bool
    {
        return $this->subscriptions()
            ->where('plan_id', $planId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Get the latest subscription for a specific plan.
     */
    public function getLatestSubscriptionForPlan(int $planId)
    {
        return $this->subscriptions()
            ->where('plan_id', $planId)
            ->latest()
            ->first();
    }

    /**
     * Check if the user has access to features (admin or active subscription).
     */
    public function hasFeatureAccess(): bool
    {
        return $this->isAdmin() || $this->hasActiveSubscription();
    }

    /**
     * Get the current plan for the user.
     */
    public function currentPlan()
    {
        return $this->activeSubscription()->with('plan');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Generate a unique API token for the user.
     */
    public function generateApiToken(): string
    {
        $this->api_token = bin2hex(random_bytes(32));
        $this->save();
        
        // Clear related caches
        $this->clearUserCaches();
        
        return $this->api_token;
    }

    /**
     * Clear user-related caches
     */
    public function clearUserCaches(): void
    {
        \Cache::forget("dashboard_stats_user_{$this->id}");
        \Cache::forget("access_status_user_{$this->id}");
    }

    /**
     * Get cached user stats
     */
    public function getCachedStats(): array
    {
        return \Cache::remember("user_stats_{$this->id}", 300, function () {
            return [
                'connections_count' => $this->whatsappConnections()->count(),
                'groups_count' => $this->whatsappGroups()->count(),
                'contacts_count' => $this->extractedContacts()->count(),
                'mass_sendings_count' => $this->massSendings()->count(),
            ];
        });
    }
}
