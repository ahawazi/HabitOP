<?php

namespace App\Actions\Habits;

use App\Enums\DefaultHabit;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EnsureDefaultHabits
{
    /**
     * Make sure the given user has the default starter habits.
     *
     * Idempotent: existing habits are matched on their stable slug and left alone,
     * including any customisation to their name, colour, order or active flag. Safe
     * to run against brand-new users, long-standing users, and repeatedly.
     *
     * @return Collection<int, Habit>
     */
    public function handle(User $user): Collection
    {
        return DB::transaction(function () use ($user): Collection {
            foreach (DefaultHabit::cases() as $default) {
                $user->habits()->firstOrCreate(
                    ['slug' => $default->value],
                    $default->toAttributes(),
                );
            }

            return $user->habits()->activeOrdered()->get();
        });
    }
}
