<?php

use App\Filament\Resources\DailyCheckIns\Pages\ManageDailyCheckIns;
use App\Filament\Resources\Habits\Pages\ManageHabits;
use App\Filament\Widgets\WeeklyHabitMatrix;
use App\Models\DailyCheckIn;
use App\Models\User;
use Livewire\Livewire;

/**
 * Every user is the super admin of their own space — and only of their own.
 */
beforeEach(function () {
    $this->me = User::factory()->create();
    $this->other = User::factory()->create();
    $this->actingAs($this->me);
});

test('the habit list only contains my habits', function () {
    Livewire::test(ManageHabits::class)
        ->assertCanSeeTableRecords($this->me->habits)
        ->assertCanNotSeeTableRecords($this->other->habits);
});

test('the history only contains my check-ins', function () {
    $mine = DailyCheckIn::factory()->forUser($this->me)->create();
    $theirs = DailyCheckIn::factory()->forUser($this->other)->create();

    Livewire::test(ManageDailyCheckIns::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

test('policies deny access to other users\' records', function () {
    $habit = $this->other->habits()->first();
    $checkIn = DailyCheckIn::factory()->forUser($this->other)->create();

    expect($this->me->can('view', $habit))->toBeFalse()
        ->and($this->me->can('update', $habit))->toBeFalse()
        ->and($this->me->can('delete', $habit))->toBeFalse()
        ->and($this->me->can('view', $checkIn))->toBeFalse()
        ->and($this->me->can('delete', $checkIn))->toBeFalse()
        ->and($this->me->can('update', $this->me->habits()->first()))->toBeTrue();
});

test('the weekly matrix refuses to toggle another user\'s habit', function () {
    Livewire::test(WeeklyHabitMatrix::class)
        ->call('toggle', $this->other->habits()->first()->id, now()->toDateString())
        ->assertNotFound();

    expect(DailyCheckIn::count())->toBe(0);
});
