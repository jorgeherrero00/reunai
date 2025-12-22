<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('stripe_subscription_item_id')->unique();
            $table->string('stripe_price_id');
            $table->string('stripe_product_id');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            $table->index(['subscription_id', 'stripe_price_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};