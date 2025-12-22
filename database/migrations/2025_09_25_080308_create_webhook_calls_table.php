<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_calls', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('type'); // evento de stripe (customer.subscription.updated, etc)
            $table->json('payload');
            $table->string('processing_status')->default('pending'); // pending, processed, failed
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'processing_status']);
            $table->index('stripe_event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_calls');
    }
};