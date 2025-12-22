<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_invoice_id')->nullable();
            $table->integer('amount'); // en centavos
            $table->string('currency')->default('eur');
            $table->string('status'); // succeeded, failed, pending, refunded
            $table->string('payment_method')->nullable(); // card, sepa_debit, etc.
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};