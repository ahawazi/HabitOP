<?php

namespace Database\Factories;

use App\Models\DailyCheckIn;
use App\Models\Habit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<DailyCheckIn>
 */
class DailyCheckInFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => Date::today()->toDateString(),
            'mood_score' => fake()->numberBetween(DailyCheckIn::MOOD_MIN, DailyCheckIn::MOOD_MAX),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Set the check-in date.
     */
    public function on(CarbonImmutable|string $date): static
    {
        return $this->state(fn (array $attributes): array => [
            'date' => $date instanceof CarbonImmutable ? $date->toDateString() : $date,
        ]);
    }

    /**
     * Assign the check-in to the given user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that no mood was recorded.
     */
    public function withoutMood(): static
    {
        return $this->state(fn (array $attributes): array => [
            'mood_score' => null,
        ]);
    }

    /**
     * Mark every active habit of the owner as completed.
     */
    public function completed(): static
    {
        return $this->partiallyCompleted(PHP_INT_MAX);
    }

    /**
     * Mark the first N active habits of the owner as completed, the rest as not.
     */
    public function partiallyCompleted(int $completedCount): static
    {
        return $this->afterCreating(function (DailyCheckIn $checkIn) use ($completedCount): void {
            $checkIn->user->habits()->activeOrdered()->get()->values()->each(
                function (Habit $habit, int $index) use ($checkIn, $completedCount): void {
                    $checkIn->completions()->create([
                        'habit_id' => $habit->id,
                        'completed' => $index < $completedCount,
                    ]);
                },
            );
        });
    }
}
