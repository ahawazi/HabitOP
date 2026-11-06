<?php

namespace App\Data;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

/**
 * The window of time a user's tracker covers.
 *
 * It starts on 1 January of the year the user registered (optionally a few years
 * earlier, see `habits.backfill_years`) and ends on 31 December of the current
 * year. Check-ins may be recorded from the start up to today; calendar and year
 * navigation is clamped to the whole window.
 */
readonly class TrackerPeriod
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {
        //
    }

    /**
     * Build the period for the given user.
     */
    public static function forUser(User $user): self
    {
        $registeredAt = $user->created_at
            ? CarbonImmutable::instance($user->created_at)
            : Date::today()->toImmutable();

        $start = $registeredAt->startOfYear()->subYears(max(0, (int) config('habits.backfill_years')));
        $end = Date::today()->toImmutable()->endOfYear()->startOfDay();

        return new self($start->startOfDay(), $end);
    }

    /**
     * Determine whether the given date falls inside the period.
     */
    public function contains(CarbonImmutable $date): bool
    {
        return $date->startOfDay()->betweenIncluded($this->start, $this->end);
    }

    /**
     * Determine whether a check-in may be recorded for the given date.
     */
    public function isRecordable(CarbonImmutable $date): bool
    {
        $date = $date->startOfDay();

        return $date->greaterThanOrEqualTo($this->start)
            && $date->lessThanOrEqualTo(Date::today()->toImmutable());
    }

    /**
     * Clamp the given date into the period.
     */
    public function clampDate(CarbonImmutable $date): CarbonImmutable
    {
        $date = $date->startOfDay();

        return match (true) {
            $date->lessThan($this->start) => $this->start,
            $date->greaterThan($this->end) => $this->end,
            default => $date,
        };
    }

    /**
     * Get the first month reachable through month navigation.
     */
    public function firstMonth(): CarbonImmutable
    {
        return $this->start->startOfMonth();
    }

    /**
     * Get the last month reachable through month navigation.
     */
    public function lastMonth(): CarbonImmutable
    {
        return $this->end->startOfMonth();
    }

    /**
     * Clamp the given month to the navigable range.
     */
    public function clampMonth(CarbonImmutable $month): CarbonImmutable
    {
        $month = $month->startOfMonth();

        return match (true) {
            $month->lessThan($this->firstMonth()) => $this->firstMonth(),
            $month->greaterThan($this->lastMonth()) => $this->lastMonth(),
            default => $month,
        };
    }

    /**
     * Get the years reachable through the year overview, oldest first.
     *
     * @return array<int, int>
     */
    public function years(): array
    {
        return range($this->start->year, $this->end->year);
    }

    /**
     * Clamp the given year to the navigable range.
     */
    public function clampYear(int $year): int
    {
        return max($this->start->year, min($this->end->year, $year));
    }
}
