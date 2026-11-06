<?php

use App\Actions\Habits\GetHabitProgress;
use App\Models\User;
use Carbon\CarbonImmutable;

/**
 * Streak rules under test:
 *  - a day counts only when every active habit is completed;
 *  - the current streak is the unbroken run ending today, and an incomplete today
 *    does not break it (the run then ends at yesterday);
 *  - the best streak is the longest unbroken run in the user's whole history.
 */
beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-07-15 09:00:00'));

    $this->user = User::factory()->create();
    $this->progress = app(GetHabitProgress::class);
});

test('no completed days means no streak', function () {
    $streaks = $this->progress->streaks($this->user);

    expect($streaks->current)->toBe(0)->and($streaks->best)->toBe(0);
});

test('a single completed day today is a streak of one', function () {
    completeDay($this->user, '2026-07-15');

    $streaks = $this->progress->streaks($this->user);

    expect($streaks->current)->toBe(1)->and($streaks->best)->toBe(1);
});

test('consecutive completed days ending today build the current streak', function () {
    foreach (['2026-07-11', '2026-07-12', '2026-07-13', '2026-07-14', '2026-07-15'] as $date) {
        completeDay($this->user, $date);
    }

    $streaks = $this->progress->streaks($this->user);

    expect($streaks->current)->toBe(5)->and($streaks->best)->toBe(5);
});

test('an incomplete today does not break a streak ending yesterday', function () {
    completeDay($this->user, '2026-07-13');
    completeDay($this->user, '2026-07-14');
    partialDay($this->user, '2026-07-15', 2);

    $streaks = $this->progress->streaks($this->user);

    expect($streaks->current)->toBe(2)->and($streaks->best)->toBe(2);
});

test('a gap breaks the streak', function () {
    completeDay($this->user, '2026-07-10');
    completeDay($this->user, '2026-07-11');
    completeDay($this->user, '2026-07-13');
    completeDay($this->user, '2026-07-14');
    completeDay($this->user, '2026-07-15');

    $streaks = $this->progress->streaks($this->user);

    expect($streaks->current)->toBe(3)->and($streaks->best)->toBe(3);
});

test('the best streak is kept after the current one breaks', function () {
    foreach (['2026-07-01', '2026-07-02', '2026-07-03', '2026-07-04'] as $date) {
        completeDay($this->user, $date);
    }

    completeDay($this->user, '2026-07-14');

    $streaks = $this->progress->streaks($this->user);

    expect($streaks->current)->toBe(1)->and($streaks->best)->toBe(4);
});

test('partially completed days never count', function () {
    partialDay($this->user, '2026-07-14', 2);
    partialDay($this->user, '2026-07-15', 1);

    expect($this->progress->streaks($this->user)->best)->toBe(0);
});

test('archiving a habit lowers the bar for future streak days', function () {
    partialDay($this->user, '2026-07-15', 2);

    $this->user->habits()->activeOrdered()->get()->last()->update(['is_active' => false]);

    expect(app(GetHabitProgress::class)->streaks($this->user)->current)->toBe(1);
});

test('a new custom habit raises the bar for a day', function () {
    completeDay($this->user, '2026-07-15');

    $this->user->habits()->create(['name' => 'Meditate', 'category' => 'mind', 'color' => '#8b5cf6']);

    expect(app(GetHabitProgress::class)->streaks($this->user)->current)->toBe(0);
});

test('streaks are personal', function () {
    $other = User::factory()->create();

    completeDay($other, '2026-07-15');

    expect($this->progress->streaks($this->user)->current)->toBe(0)
        ->and($this->progress->streaks($other)->current)->toBe(1);
});
