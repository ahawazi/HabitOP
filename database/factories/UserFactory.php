<?php

namespace Database\Factories;

use App\Actions\Habits\EnsureDefaultHabits;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'locale' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Configure the model factory: every user starts with the default habits,
     * exactly as a real registration does.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            app(EnsureDefaultHabits::class)->handle($user);
        });
    }

    /**
     * Indicate that the user should start without any habits.
     *
     * Drops the after-creating callbacks registered so far, which is where the
     * default habits are created.
     */
    public function withoutHabits(): static
    {
        return $this->newInstance(['afterCreating' => new Collection]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
