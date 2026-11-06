<?php

namespace App\Actions\Habits;

use App\Data\TrackerPeriod;
use App\Models\DailyCheckIn;
use App\Models\Habit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * One-tap toggle of a single habit on a single day (HabitFlow's weekly matrix).
 *
 * It writes to the same check-in rows as the full daily check-in form, so both
 * views always agree.
 */
class ToggleHabitCompletion
{
    /**
     * Flip the habit's completion for the date and return the new state.
     *
     * @throws AuthorizationException When the habit belongs to someone else.
     * @throws ValidationException When the date cannot be recorded.
     */
    public function handle(User $user, Habit $habit, CarbonImmutable $date): bool
    {
        if ($habit->user_id !== $user->id) {
            throw new AuthorizationException;
        }

        $date = $date->startOfDay();

        if (! TrackerPeriod::forUser($user)->isRecordable($date)) {
            throw ValidationException::withMessages([
                'date' => __('habits.validation.future_date'),
            ]);
        }

        return DB::transaction(function () use ($user, $habit, $date): bool {
            $checkIn = DailyCheckIn::query()
                ->ownedBy($user)
                ->whereDate('date', $date->toDateString())
                ->first();

            if (! $checkIn) {
                $checkIn = new DailyCheckIn(['date' => $date->toDateString()]);
                $checkIn->user()->associate($user);
                $checkIn->save();
            }

            $completion = $checkIn->completions()->firstOrNew(['habit_id' => $habit->id]);
            $completion->completed = ! $completion->completed;
            $completion->save();

            return $completion->completed;
        });
    }
}
