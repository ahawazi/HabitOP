<?php

namespace App\Actions\Habits;

use App\Data\TrackerPeriod;
use App\Models\DailyCheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveDailyCheckIn
{
    /**
     * Create or update the user's personal check-in for a single date.
     *
     * The check-in row is created on first save, which is why the tracker never
     * pre-creates rows for the thousands of dates it displays.
     *
     * @param  array<int|string, bool>  $completions  Keyed by habit ID.
     *
     * @throws ValidationException When the date lies in the future or outside the tracked period.
     */
    public function handle(
        User $user,
        CarbonImmutable $date,
        array $completions,
        ?int $moodScore = null,
        ?string $notes = null,
    ): DailyCheckIn {
        $date = $date->startOfDay();

        if ($date->greaterThan(Date::today()->toImmutable())) {
            throw ValidationException::withMessages([
                'date' => __('habits.validation.future_date'),
            ]);
        }

        if (! TrackerPeriod::forUser($user)->isRecordable($date)) {
            throw ValidationException::withMessages([
                'date' => __('habits.validation.invalid_date'),
            ]);
        }

        if ($moodScore !== null && ($moodScore < DailyCheckIn::MOOD_MIN || $moodScore > DailyCheckIn::MOOD_MAX)) {
            throw ValidationException::withMessages([
                'mood_score' => __('habits.validation.mood_range', ['min' => DailyCheckIn::MOOD_MIN, 'max' => DailyCheckIn::MOOD_MAX]),
            ]);
        }

        $notes = $notes !== null && trim($notes) !== '' ? trim($notes) : null;

        return DB::transaction(function () use ($user, $date, $completions, $moodScore, $notes): DailyCheckIn {
            $checkIn = DailyCheckIn::query()
                ->ownedBy($user)
                ->whereDate('date', $date->toDateString())
                ->first();

            if (! $checkIn) {
                // user_id is deliberately not mass-assignable: ownership is set here
                // from the authenticated user only.
                $checkIn = new DailyCheckIn(['date' => $date->toDateString()]);
                $checkIn->user()->associate($user);
            }

            $checkIn->fill([
                'mood_score' => $moodScore,
                'notes' => $notes,
            ])->save();

            // Only the user's own active habits may be recorded, so a forged habit ID
            // belonging to someone else is silently discarded.
            $habitIds = $user->habits()->activeOrdered()->pluck('id');

            foreach ($habitIds as $habitId) {
                $checkIn->completions()->updateOrCreate(
                    ['habit_id' => $habitId],
                    ['completed' => (bool) ($completions[$habitId] ?? false)],
                );
            }

            return $checkIn->load('completions');
        });
    }
}
