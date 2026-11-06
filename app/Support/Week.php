<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;

/**
 * Locale-aware week boundaries (Saturday in Persian, Monday in English).
 */
class Week
{
    /**
     * Get the first day of the week for the current locale (Carbon day constant).
     */
    public static function startsOn(): int
    {
        $config = (array) config('habits.week_starts_on');

        return (int) ($config[App::getLocale()] ?? $config['default'] ?? CarbonImmutable::MONDAY);
    }

    /**
     * Get the first day of the week containing the given date.
     */
    public static function start(?CarbonImmutable $date = null): CarbonImmutable
    {
        return ($date ?? Date::today()->toImmutable())->startOfWeek(self::startsOn())->startOfDay();
    }

    /**
     * Get the last day of the week containing the given date.
     */
    public static function end(?CarbonImmutable $date = null): CarbonImmutable
    {
        return self::start($date)->addDays(6);
    }

    /**
     * Get the seven dates of the week containing the given date.
     *
     * @return array<int, CarbonImmutable>
     */
    public static function days(?CarbonImmutable $date = null): array
    {
        $start = self::start($date);

        return array_map(fn (int $offset): CarbonImmutable => $start->addDays($offset), range(0, 6));
    }
}
