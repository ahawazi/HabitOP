<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Every row is owned by exactly one user: each user is the sole administrator of
     * their own habits and check-ins, and nobody else can ever read them.
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Stable key for the seeded default habits (exercise, reading, ...);
            // null for habits the user created themselves.
            $table->string('slug')->nullable();
            $table->string('category')->default('health');
            $table->string('color', 20)->default('#10b981');
            $table->string('icon')->nullable();
            $table->unsignedTinyInteger('target_per_week')->default(7);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'slug']);
            $table->index(['user_id', 'is_active', 'sort_order']);
        });

        Schema::create('daily_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('mood_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Also serves every "this user, this date range" lookup.
            $table->unique(['user_id', 'date']);
        });

        Schema::create('habit_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_check_in_id')->constrained()->cascadeOnDelete();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->unique(['daily_check_in_id', 'habit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_completions');
        Schema::dropIfExists('daily_check_ins');
        Schema::dropIfExists('habits');
    }
};
