<?php

use App\Actions\Habits\GetHabitProgress;
use App\Actions\Habits\ToggleHabitCompletion;
use App\Data\DailyProgress;
use App\Models\DailyCheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;

beforeEach(function () {
    // Wednesday 15 July 2026.
    $this->travelTo(CarbonImmutable::parse('2026-07-15 09:00:00'));

    $this->user = User::factory()->create();
    $this->progress = app(GetHabitProgress::class);
});

test('a range returns every date, with empty days for missing check-ins', function () {
    partialDay($this->user, '2026-07-13', 2);

    $range = $this->progress->forRange($this->user, CarbonImmutable::parse('2026-07-12'), CarbonImmutable::parse('2026-07-16'));

    expect($range->keys()->all())->toBe(['2026-07-12', '2026-07-13', '2026-07-14', '2026-07-15', '2026-07-16'])
        ->and($range['2026-07-13']->completedCount)->toBe(2)
        ->and($range['2026-07-13']->percentage)->toBe(67)
        ->and($range['2026-07-12']->hasCheckIn)->toBeFalse()
        ->and($range['2026-07-15']->isToday)->toBeTrue()
        ->and($range['2026-07-16']->isFuture)->toBeTrue();
});

test('summaries ignore future days', function () {
    completeDay($this->user, '2026-07-14');
    completeDay($this->user, '2026-07-15');

    $summary = $this->progress->summarize(
        $this->progress->forRange($this->user, CarbonImmutable::parse('2026-07-14'), CarbonImmutable::parse('2026-07-20')),
    );

    expect($summary->totalDays)->toBe(2)
        ->and($summary->completedDays)->toBe(2)
        ->and($summary->percentage)->toBe(100);
});

test('average mood only counts days with a recorded mood', function () {
    DailyCheckIn::factory()->forUser($this->user)->on('2026-07-13')->create(['mood_score' => 6]);
    DailyCheckIn::factory()->forUser($this->user)->on('2026-07-14')->create(['mood_score' => 9]);
    DailyCheckIn::factory()->forUser($this->user)->on('2026-07-15')->withoutMood()->create();

    $summary = $this->progress->summarize(
        $this->progress->forRange($this->user, CarbonImmutable::parse('2026-07-13'), CarbonImmutable::parse('2026-07-15')),
    );

    expect($summary->averageMood)->toBe(7.5);
});

test('completions of archived habits no longer count', function () {
    completeDay($this->user, '2026-07-15');

    $this->user->habits()->activeOrdered()->first()->update(['is_active' => false]);

    $day = app(GetHabitProgress::class)->forDate($this->user, CarbonImmutable::parse('2026-07-15'));

    expect($day->completedCount)->toBe(2)->and($day->totalCount)->toBe(2)->and($day->isComplete())->toBeTrue();
});

test('progress levels scale with any number of habits', function (int $completed, int $total, int $level) {
    $day = new DailyProgress(
        date: CarbonImmutable::parse('2026-07-15'),
        completedCount: $completed,
        totalCount: $total,
        percentage: DailyProgress::percentageFor($completed, $total),
    );

    expect($day->level())->toBe($level);
})->with([
    [0, 5, 0],
    [1, 5, 1],
    [3, 5, 2],
    [5, 5, 3],
    [0, 0, 0],
]);

test('weekly stats track each habit against its weekly target', function () {
    App::setLocale('en'); // Weeks start on Monday 13 July.

    $habit = $this->user->habits()->activeOrdered()->first();
    $habit->update(['target_per_week' => 2]);

    $toggle = app(ToggleHabitCompletion::class);
    $toggle->handle($this->user, $habit, CarbonImmutable::parse('2026-07-13'));
    $toggle->handle($this->user, $habit, CarbonImmutable::parse('2026-07-14'));
    $toggle->handle($this->user, $habit, CarbonImmutable::parse('2026-07-12')); // previous week

    $stats = app(GetHabitProgress::class)->weeklyHabitStats($this->user);
    $first = $stats->first();

    expect($first->habit->is($habit))->toBeTrue()
        ->and($first->completedDates)->toEqualCanonicalizing(['2026-07-13', '2026-07-14'])
        ->and($first->percentage())->toBe(100)
        ->and($first->isTargetMet())->toBeTrue()
        // 2 of 2 + 0 of 7 + 0 of 7
        ->and(app(GetHabitProgress::class)->weeklyGoalRate($stats))->toBe(13);
});

test('persian weeks start on saturday', function () {
    App::setLocale('fa');

    $habit = $this->user->habits()->activeOrdered()->first();
    app(ToggleHabitCompletion::class)->handle($this->user, $habit, CarbonImmutable::parse('2026-07-11')); // Saturday

    expect(app(GetHabitProgress::class)->weeklyHabitStats($this->user)->first()->completedCount())->toBe(1);

    App::setLocale('en');

    expect(app(GetHabitProgress::class)->weeklyHabitStats($this->user)->first()->completedCount())->toBe(0);
});
