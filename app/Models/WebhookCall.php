<?php
// app/Models/WebhookCall.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookCall extends Model
{
    protected $fillable = [
        'stripe_event_id',
        'type',
        'payload',
        'processing_status',
        'processing_error',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function markAsProcessed(): void
    {
        $this->update([
            'processing_status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'processing_status' => 'failed',
            'processing_error' => $error,
            'processed_at' => now(),
        ]);
    }
}