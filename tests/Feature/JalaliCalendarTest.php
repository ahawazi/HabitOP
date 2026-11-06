<?php

use App\Data\TrackerPeriod;
use App\Filament\Pages\Calendar;
use App\Filament\Pages\CheckIn;
use App\Filament\Pages\YearOverview;
use App\Filament\Resources\DailyCheckIns\DailyCheckInResource;
use App\Models\User;
use App\Support\LocalCalendar;
use App\Support\Locale;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

beforeEach(function () {
    // Saturday 16 Aban 1405.
    $this->travelTo(CarbonImmutable::parse('2026-11-07 09:00:00'));
});

describe('in persian', function () {
    beforeEach(function () {
        Locale::apply('fa');
    });

    test('dates read in the jalali calendar with persian digits', function () {
        $date = CarbonImmutable::parse('2026-11-07');

        expect(LocalCalendar::isJalali())->toBeTrue()
            ->and(LocalCalendar::format($date, __('habits.formats.long_date')))->toBe('شنبه ۱۶ آبان ۱۴۰۵')
            ->and(LocalCalendar::format($date, __('habits.formats.day_month')))->toBe('۱۶ آبان')
            ->and(LocalCalendar::digits(1405))->toBe('۱۴۰۵');
    });

    test('months and years follow the jalali calendar', function () {
        $date = CarbonImmutable::parse('2026-11-07');

        expect(LocalCalendar::year($date))->toBe(1405)
            ->and(LocalCalendar::startOfMonth($date)->toDateString())->toBe('2026-10-23')
            ->and(LocalCalendar::endOfMonth($date)->toDateString())->toBe('2026-11-21')
            ->and(LocalCalendar::startOfYear($date)->toDateString())->toBe('2026-03-21')
            ->and(LocalCalendar::endOfYear($date)->toDateString())->toBe('2027-03-20')
            ->and(LocalCalendar::addMonths($date, -1)->toDateString())->toBe('2026-09-23')
            ->and(LocalCalendar::addMonths(CarbonImmutable::parse('2027-03-20'), 1)->toDateString())->toBe('2027-03-21')
            ->and(LocalCalendar::monthKey($date))->toBe('1405-08')
            ->and(LocalCalendar::parseMonthKey('1405-08')?->toDateString())->toBe('2026-10-23')
            ->and(LocalCalendar::parseMonthKey('1405-13'))->toBeNull()
            ->and(LocalCalendar::parseMonthKey('nope'))->toBeNull()
            ->and(LocalCalendar::monthsOfYear(1405))->toHaveCount(12);
    });

    test('the tracked period spans whole jalali years', function () {
        // 12 Bahman 1404.
        $user = User::factory()->create(['created_at' => '2026-02-01']);

        $period = TrackerPeriod::forUser($user);

        expect($period->start->toDateString())->toBe('2025-03-21')
            ->and($period->end->toDateString())->toBe('2027-03-20')
            ->and($period->years())->toBe([1404, 1405]);
    });

    test('the calendar navigates jalali months', function () {
        $user = User::factory()->create(['created_at' => '2026-02-01', 'locale' => 'fa']);
        $this->actingAs($user);

        Livewire::test(Calendar::class)
            ->assertSet('month', '1405-08')
            ->assertSee('آبان ۱۴۰۵')
            ->call('nextMonth')
            ->assertSet('month', '1405-09')
            ->assertSee('آذر ۱۴۰۵');

        Livewire::withQueryParams(['month' => '1390-01'])
            ->test(Calendar::class)
            ->assertSet('month', '1404-01');
    });

    test('the check-in page links to the jalali month', function () {
        $user = User::factory()->create(['created_at' => '2026-02-01', 'locale' => 'fa']);
        $this->actingAs($user);

        expect(Livewire::test(CheckIn::class)->instance()->calendarUrl())->toContain('month=1405-08');
    });

    test('the year overview shows a jalali year', function () {
        $user = User::factory()->create(['created_at' => '2026-02-01', 'locale' => 'fa']);
        $this->actingAs($user);

        Livewire::test(YearOverview::class)
            ->assertSet('year', 1405)
            ->assertSee('فروردین')
            ->assertSee('اسفند');
    });

    test('panel pages render jalali dates', function () {
        $user = User::factory()->create(['created_at' => '2026-02-01', 'locale' => 'fa']);
        completeDay($user, '2026-11-05');
        $this->actingAs($user);

        $this->get(DailyCheckInResource::getUrl('index'))->assertOk()->assertSee('پنجشنبه ۱۴ آبان ۱۴۰۵');
        $this->get(Calendar::getUrl())->assertOk()->assertSee('آبان ۱۴۰۵');
        $this->get(YearOverview::getUrl())->assertOk();
    });
});

describe('in english', function () {
    beforeEach(function () {
        Locale::apply('en');
    });

    test('dates stay gregorian', function () {
        $date = CarbonImmutable::parse('2026-11-07');

        expect(LocalCalendar::isJalali())->toBeFalse()
            ->and(LocalCalendar::format($date, __('habits.formats.long_date')))->toBe('Saturday, 7 November 2026')
            ->and(LocalCalendar::startOfMonth($date)->toDateString())->toBe('2026-11-01')
            ->and(LocalCalendar::endOfYear($date)->toDateString())->toBe('2026-12-31')
            ->and(LocalCalendar::monthKey($date))->toBe('2026-11')
            ->and(LocalCalendar::addMonths(CarbonImmutable::parse('2026-01-31'), 1)->toDateString())->toBe('2026-02-01');
    });
});
