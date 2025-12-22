<?php
// app/Models/Subscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_price_id',
        'plan',
        'status',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'canceled_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Helper methods
    public function active(): bool
    {
        return $this->status === 'active' && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function canceled(): bool
    {
        return $this->status === 'canceled' || $this->canceled_at !== null;
    }

    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }
}