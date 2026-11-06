<?php

namespace App\Data;

use App\Models\Habit;

/**
 * One habit's progress towards its weekly target (the HabitFlow model).
 */
readonly class WeeklyHabitStat
{
    /**
     * @param  array<int, string>  $completedDates  "Y-m-d" dates inside the week.
     */
    public function __construct(
        public Habit $habit,
        public array $completedDates,
        public int $target,
    ) {
        //
    }

    /**
     * Get the number of days the habit was completed this week.
     */
    public function completedCount(): int
    {
        return count($this->completedDates);
    }

    /**
     * Get the progress towards the weekly target, capped at 100.
     */
    public function percentage(): int
    {
        if ($this->target <= 0) {
            return 0;
        }

        return (int) min(100, round($this->completedCount() / $this->target * 100));
    }

    /**
     * Determine whether the weekly target has been met.
     */
    public function isTargetMet(): bool
    {
        return $this->completedCount() >= $this->target;
    }

    /**
     * Determine whether the habit was completed on the given date.
     */
    public function isCompletedOn(string $date): bool
    {
        return in_array($date, $this->completedDates, true);
    }
}
