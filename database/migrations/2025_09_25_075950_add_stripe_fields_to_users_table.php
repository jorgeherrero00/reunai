<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('email');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->string('plan')->default('free')->after('stripe_subscription_id'); // free, starter, pro
            $table->timestamp('subscription_ends_at')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_subscription_id', 'plan', 'subscription_ends_at']);
        });
    }
};