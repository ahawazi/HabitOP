<?php

namespace Database\Factories;

use App\Enums\DefaultHabit;
use App\Enums\HabitCategory;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit>
 */
class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(HabitCategory::cases());

        return [
            'user_id' => User::factory()->withoutHabits(),
            'name' => ucfirst(rtrim(fake()->unique()->sentence(3), '.')),
            'slug' => null,
            'category' => $category->value,
            'color' => $category->defaultHex(),
            'icon' => null,
            'target_per_week' => fake()->numberBetween(1, 7),
            'sort_order' => fake()->numberBetween(10, 100),
            'is_active' => true,
        ];
    }

    /**
     * Assign the habit to the given user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Use one of the default habits' attributes.
     */
    public function default(DefaultHabit $default): static
    {
        return $this->state(fn (array $attributes): array => [
            'slug' => $default->value,
            ...$default->toAttributes(),
        ]);
    }

    /**
     * Indicate that the habit is archived.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
