<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // database/migrations/xxxx_xx_xx_create_tasks_table.php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
    $table->string('descripcion');
    $table->boolean('completada')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
