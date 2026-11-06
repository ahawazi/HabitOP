<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;
use Throwable;

/**
 * The active calendar system: the signed-in user's choice (Settings), otherwise
 * the language's default (Jalali in Persian, Gregorian in English).
 *
 * Dates are always stored and passed around as Gregorian `CarbonImmutable`s; this
 * class only decides where months and years begin and end, and how dates read.
 */
class LocalCalendar
{
    public const string JALALI = 'jalali';

    public const string GREGORIAN = 'gregorian';

    /**
     * Get the supported calendar systems as key => translated name.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::JALALI => __('habits.settings.calendars.jalali'),
            self::GREGORIAN => __('habits.settings.calendars.gregorian'),
        ];
    }

    /**
     * Determine whether the given value is a supported calendar system.
     */
    public static function isSupported(?string $system): bool
    {
        return in_array($system, [self::JALALI, self::GREGORIAN], true);
    }

    /**
     * Get the calendar system a language uses by default.
     */
    public static function defaultFor(?string $locale = null): string
    {
        $config = (array) config('habits.calendars');
        $system = $config[$locale ?? App::getLocale()] ?? $config['default'] ?? self::GREGORIAN;

        return self::isSupported($system) ? $system : self::GREGORIAN;
    }

    /**
     * Get the active calendar system.
     */
    public static function system(): string
    {
        $user = Auth::user();

        return $user instanceof User && self::isSupported($user->calendar)
            ? (string) $user->calendar
            : self::defaultFor();
    }

    /**
     * Get the calendar system that is not active (shown as the "equivalent" date).
     */
    public static function alternate(): string
    {
        return self::isJalali() ? self::GREGORIAN : self::JALALI;
    }

    /**
     * Determine whether the active calendar is Jalali.
     */
    public static function isJalali(): bool
    {
        return self::system() === self::JALALI;
    }

    /**
     * Format a date with a `habits.formats.*` pattern in the active (or given) calendar.
     *
     * Month names come from `habits.months.*`; weekday and time tokens are taken
     * from Carbon, since they do not depend on the calendar.
     */
    public static function format(CarbonInterface $date, string $format, ?string $system = null): string
    {
        $formatted = ($system ?? self::system()) === self::JALALI
            ? self::formatJalali($date, $format)
            : $date->translatedFormat($format);

        return self::digits($formatted);
    }

    /**
     * Describe the span between two dates by month, e.g. "Oct – Nov 2026".
     */
    public static function monthSpan(CarbonImmutable $start, CarbonImmutable $end, ?string $system = null): string
    {
        $startLabel = self::format($start, __('habits.formats.month_year'), $system);
        $endLabel = self::format($end, __('habits.formats.month_year'), $system);

        if ($startLabel === $endLabel) {
            return $startLabel;
        }

        $sameYear = self::format($start, 'Y', $system) === self::format($end, 'Y', $system);

        return ($sameYear ? self::format($start, __('habits.formats.month'), $system) : $startLabel).' – '.$endLabel;
    }

    /**
     * Write numbers with the active language's digits (Persian digits in Persian).
     */
    public static function digits(int|string $value): string
    {
        return App::isLocale('fa')
            ? CalendarUtils::convertNumbers((string) $value)
            : (string) $value;
    }

    /**
     * Get the calendar year the given date belongs to.
     */
    public static function year(CarbonImmutable $date): int
    {
        return self::isJalali() ? (int) self::jalali($date)->getYear() : $date->year;
    }

    /**
     * Get the first day of the given calendar year.
     */
    public static function yearStart(int $year): CarbonImmutable
    {
        return self::isJalali()
            ? self::gregorian(new Jalalian($year, 1, 1))
            : CarbonImmutable::create($year, 1, 1)->startOfDay();
    }

    /**
     * Get the first day of the year containing the given date.
     */
    public static function startOfYear(CarbonImmutable $date): CarbonImmutable
    {
        return self::yearStart(self::year($date));
    }

    /**
     * Get the last day of the year containing the given date.
     */
    public static function endOfYear(CarbonImmutable $date): CarbonImmutable
    {
        return self::yearStart(self::year($date) + 1)->subDay();
    }

    /**
     * Get the first day of the month containing the given date.
     */
    public static function startOfMonth(CarbonImmutable $date): CarbonImmutable
    {
        return self::isJalali()
            ? self::gregorian(self::jalali($date)->getFirstDayOfMonth())
            : $date->startOfMonth()->startOfDay();
    }

    /**
     * Get the last day of the month containing the given date.
     */
    public static function endOfMonth(CarbonImmutable $date): CarbonImmutable
    {
        return self::isJalali()
            ? self::gregorian(self::jalali($date)->getEndDayOfMonth())
            : $date->endOfMonth()->startOfDay();
    }

    /**
     * Move the month containing the given date by a number of months (returns its first day).
     */
    public static function addMonths(CarbonImmutable $date, int $months): CarbonImmutable
    {
        $start = self::startOfMonth($date);

        if (! self::isJalali()) {
            return $start->addMonthsNoOverflow($months);
        }

        // Jalalian rejects a zero step.
        if ($months === 0) {
            return $start;
        }

        $jalali = self::jalali($start);

        return self::gregorian($months > 0 ? $jalali->addMonths($months) : $jalali->subMonths(-$months));
    }

    /**
     * Determine whether two dates fall in the same calendar month.
     */
    public static function isSameMonth(CarbonImmutable $first, CarbonImmutable $second): bool
    {
        return self::startOfMonth($first)->isSameDay(self::startOfMonth($second));
    }

    /**
     * Get the first day of each month of the given calendar year.
     *
     * @return array<int, CarbonImmutable>
     */
    public static function monthsOfYear(int $year): array
    {
        $start = self::yearStart($year);

        return array_map(fn (int $offset): CarbonImmutable => self::addMonths($start, $offset), range(0, 11));
    }

    /**
     * Get the month containing the given date as a "Y-m" key in the active calendar.
     */
    public static function monthKey(CarbonImmutable $date): string
    {
        return self::isJalali() ? self::jalali($date)->format('Y-m') : $date->format('Y-m');
    }

    /**
     * Parse a "Y-m" key of the active calendar into the month's first day.
     */
    public static function parseMonthKey(?string $key): ?CarbonImmutable
    {
        if ($key === null || ! preg_match('/^(\d{4})-(\d{2})$/', $key, $matches)) {
            return null;
        }

        [, $year, $month] = array_map('intval', $matches);

        if ($month < 1 || $month > 12) {
            return null;
        }

        try {
            return self::isJalali()
                ? self::gregorian(new Jalalian($year, $month, 1))
                : CarbonImmutable::create($year, $month, 1)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Format a date in the Jalali calendar using PHP date() tokens.
     */
    protected static function formatJalali(CarbonInterface $date, string $format): string
    {
        $jalali = self::jalali($date);
        $output = '';
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            $token = $format[$i];

            if ($token === '\\') {
                $output .= $format[++$i] ?? '';

                continue;
            }

            $output .= match ($token) {
                'd' => sprintf('%02d', $jalali->getDay()),
                'j' => (string) $jalali->getDay(),
                'm' => sprintf('%02d', $jalali->getMonth()),
                'n' => (string) $jalali->getMonth(),
                'F', 'M' => __('habits.months.jalali.'.$jalali->getMonth()),
                't' => (string) $jalali->getMonthDays(),
                'Y' => (string) $jalali->getYear(),
                'y' => substr((string) $jalali->getYear(), -2),
                default => ctype_alpha($token) ? $date->translatedFormat($token) : $token,
            };
        }

        return $output;
    }

    protected static function jalali(CarbonInterface $date): Jalalian
    {
        return Jalalian::fromCarbon(Carbon::instance($date));
    }

    protected static function gregorian(Jalalian $date): CarbonImmutable
    {
        return CarbonImmutable::instance($date->toCarbon())->startOfDay();
    }
}
