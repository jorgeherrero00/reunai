<?php
// app/Models/SubscriptionItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionItem extends Model
{
    protected $fillable = [
        'subscription_id',
        'stripe_subscription_item_id',
        'stripe_price_id',
        'stripe_product_id',
        'quantity',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}