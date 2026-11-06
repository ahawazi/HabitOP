<?php

use App\Filament\Pages\Calendar;
use App\Filament\Pages\CheckIn;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\YearOverview;
use App\Filament\Resources\DailyCheckIns\DailyCheckInResource;
use App\Filament\Resources\Habits\HabitResource;
use App\Filament\Widgets\WeeklyHabitMatrix;
use App\Models\DailyCheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-07-15 09:00:00'));

    $this->user = User::factory()->create(['created_at' => '2026-02-01']);
    $this->actingAs($this->user);
});

test('every panel page renders for a signed-in user', function (string $url) {
    completeDay($this->user, '2026-07-14');

    $this->get($url)->assertOk();
})->with([
    'dashboard' => fn () => Dashboard::getUrl(),
    'check-in' => fn () => CheckIn::getUrl(),
    'check-in for a past day' => fn () => CheckIn::getUrl(['date' => '2026-07-01']),
    'check-in for a future day' => fn () => CheckIn::getUrl(['date' => '2026-08-01']),
    'calendar' => fn () => Calendar::getUrl(),
    'calendar for a month' => fn () => Calendar::getUrl(['month' => '2026-03']),
    'year' => fn () => YearOverview::getUrl(),
    'habits' => fn () => HabitResource::getUrl('index'),
    'history' => fn () => DailyCheckInResource::getUrl('index'),
]);

test('the check-in page saves habits, mood and notes', function () {
    $habits = $this->user->habits()->activeOrdered()->get();

    Livewire::test(CheckIn::class)
        ->assertSet('date', '2026-07-15')
        ->fillForm([
            'completed' => [(string) $habits[0]->id, (string) $habits[1]->id],
            'mood_score' => 8,
            'notes' => 'Felt great',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $checkIn = DailyCheckIn::query()->ownedBy($this->user)->sole();

    expect($checkIn->date->toDateString())->toBe('2026-07-15')
        ->and($checkIn->mood_score)->toBe(8)
        ->and($checkIn->notes)->toBe('Felt great')
        ->and($checkIn->completedCount())->toBe(2);
});

test('the check-in page loads an existing day and navigates between days', function () {
    DailyCheckIn::factory()->forUser($this->user)->on('2026-07-14')->partiallyCompleted(1)->create(['mood_score' => 4, 'notes' => 'Meh']);

    Livewire::withQueryParams(['date' => '2026-07-14'])
        ->test(CheckIn::class)
        ->assertSchemaStateSet(['mood_score' => 4, 'notes' => 'Meh'])
        ->call('nextDay')
        ->assertSet('date', '2026-07-15')
        ->assertSchemaStateSet(['mood_score' => null, 'notes' => null]);
});

test('requested dates are clamped into the tracked period', function () {
    Livewire::withQueryParams(['date' => '1999-01-01'])
        ->test(CheckIn::class)
        ->assertSet('date', '2026-01-01');

    Livewire::withQueryParams(['date' => 'not-a-date'])
        ->test(CheckIn::class)
        ->assertSet('date', '2026-07-15');
});

test('future days cannot be saved from the check-in page', function () {
    Livewire::withQueryParams(['date' => '2026-07-20'])
        ->test(CheckIn::class)
        ->call('save')
        ->assertNotified();

    expect(DailyCheckIn::count())->toBe(0);
});

test('the weekly matrix toggles a habit and refreshes the other widgets', function () {
    $habit = $this->user->habits()->activeOrdered()->first();

    Livewire::test(WeeklyHabitMatrix::class)
        ->call('toggle', $habit->id, '2026-07-15')
        ->assertDispatched('habit-progress-updated');

    expect(DailyCheckIn::query()->ownedBy($this->user)->sole()->completedCount())->toBe(1);
});

test('the calendar shows the selected month', function () {
    Livewire::withQueryParams(['month' => '2026-03'])
        ->test(Calendar::class)
        ->assertSet('month', '2026-03')
        ->call('nextMonth')
        ->assertSet('month', '2026-04');
});

test('the calendar cannot go before the tracked period', function () {
    Livewire::withQueryParams(['month' => '2020-01'])
        ->test(Calendar::class)
        ->assertSet('month', '2026-01');
});
