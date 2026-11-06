<?php

use App\Actions\Habits\ToggleHabitCompletion;
use App\Models\DailyCheckIn;
use App\Models\Habit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-07-15 09:00:00'));

    $this->user = User::factory()->create();
    $this->habit = $this->user->habits()->activeOrdered()->first();
    $this->toggle = app(ToggleHabitCompletion::class);
});

test('toggling ticks and unticks a habit for the day', function () {
    $date = CarbonImmutable::parse('2026-07-14');

    expect($this->toggle->handle($this->user, $this->habit, $date))->toBeTrue();

    $checkIn = DailyCheckIn::query()->ownedBy($this->user)->sole();
    expect($checkIn->completedCount())->toBe(1);

    expect($this->toggle->handle($this->user, $this->habit, $date))->toBeFalse()
        ->and($checkIn->fresh()->completedCount())->toBe(0);
});

test('toggling keeps an existing mood and note', function () {
    DailyCheckIn::factory()->forUser($this->user)->on('2026-07-15')->create(['mood_score' => 8, 'notes' => 'Kept']);

    $this->toggle->handle($this->user, $this->habit, CarbonImmutable::parse('2026-07-15'));

    $checkIn = DailyCheckIn::query()->ownedBy($this->user)->sole();

    expect($checkIn->mood_score)->toBe(8)->and($checkIn->notes)->toBe('Kept');
});

test('someone else\'s habit cannot be toggled', function () {
    $this->toggle->handle($this->user, Habit::factory()->create(), CarbonImmutable::parse('2026-07-15'));
})->throws(AuthorizationException::class);

test('future days cannot be toggled', function () {
    $this->toggle->handle($this->user, $this->habit, CarbonImmutable::parse('2026-07-16'));
})->throws(ValidationException::class);
