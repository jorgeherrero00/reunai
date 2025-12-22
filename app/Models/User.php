<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;



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
        'api_token',
        'stripe_customer_id',
        'stripe_subscription_id',
        'plan',
        'subscription_ends_at',
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


    public function meetings()
{
    return $this->hasMany(Meeting::class);
}

public function integrations()
{
    return $this->hasMany(UserIntegration::class);
}

// Métodos helper para planes
public function isOnPlan($plan)
{
    return $this->plan === $plan;
}

public function canUploadMeetings()
{
    $limits = [
        'free' => 1,
        'starter' => 5,
        'pro' => 20,
    ];
    
    $monthlyUploads = $this->meetings()
        ->whereMonth('created_at', now()->month)
        ->count();
    
    return $monthlyUploads < ($limits[$this->plan] ?? 0);
}

public function isSubscribed()
{
    return $this->plan !== 'free' && 
           $this->subscription_ends_at && 
           $this->subscription_ends_at->isFuture();
}

public function subscriptions(): HasMany
{
    return $this->hasMany(Subscription::class);
}

public function subscription(): HasOne
{
    return $this->hasOne(Subscription::class)->latestOfMany();
}

public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}

// Helper methods
public function subscribed(string $plan = null): bool
{
    $subscription = $this->subscription;

    if (!$subscription || !$subscription->active()) {
        return false;
    }

    return $plan ? $subscription->plan === $plan : true;
}

public function onTrial(): bool
{
    return $this->subscription && $this->subscription->onTrial();
}

}
