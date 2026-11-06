<?php

use App\Actions\Habits\SaveDailyCheckIn;
use App\Models\DailyCheckIn;
use App\Models\Habit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->travelTo(CarbonImmutable::parse('2026-07-15 09:00:00'));

    $this->user = User::factory()->create(['created_at' => '2026-03-01']);
    $this->habits = $this->user->habits()->activeOrdered()->get();
    $this->save = app(SaveDailyCheckIn::class);
});

test('saving creates a check-in with a completion row for every active habit', function () {
    $checkIn = $this->save->handle(
        $this->user,
        CarbonImmutable::parse('2026-07-15'),
        [$this->habits[0]->id => true, $this->habits[2]->id => true],
        7,
        '  Good day  ',
    );

    expect($checkIn->user_id)->toBe($this->user->id)
        ->and($checkIn->mood_score)->toBe(7)
        ->and($checkIn->notes)->toBe('Good day')
        ->and($checkIn->completions)->toHaveCount(3)
        ->and($checkIn->completedCount())->toBe(2);
});

test('saving the same date again updates the existing row', function () {
    $date = CarbonImmutable::parse('2026-07-14');

    $this->save->handle($this->user, $date, [$this->habits[0]->id => true], 5);
    $this->save->handle($this->user, $date, [], null, null);

    $checkIn = DailyCheckIn::query()->ownedBy($this->user)->sole();

    expect($checkIn->mood_score)->toBeNull()
        ->and($checkIn->completedCount())->toBe(0)
        ->and($checkIn->completions)->toHaveCount(3);
});

test('a future date is rejected', function () {
    $this->save->handle($this->user, CarbonImmutable::parse('2026-07-16'), []);
})->throws(ValidationException::class);

test('a date before the tracked period is rejected', function () {
    $this->save->handle($this->user, CarbonImmutable::parse('2025-12-31'), []);
})->throws(ValidationException::class);

test('dates since the start of the sign-up year can be backfilled', function () {
    $checkIn = $this->save->handle($this->user, CarbonImmutable::parse('2026-01-01'), []);

    expect($checkIn->date->toDateString())->toBe('2026-01-01');
});

test('an out of range mood is rejected', function (int $mood) {
    $this->save->handle($this->user, CarbonImmutable::parse('2026-07-15'), [], $mood);
})->with([0, 11])->throws(ValidationException::class);

test('habit ids belonging to another user are ignored', function () {
    $foreign = Habit::factory()->create();

    $checkIn = $this->save->handle($this->user, CarbonImmutable::parse('2026-07-15'), [$foreign->id => true]);

    expect($checkIn->completions->pluck('habit_id'))->not->toContain($foreign->id)
        ->and($foreign->completions()->count())->toBe(0);
});

test('archived habits get no completion rows', function () {
    $this->habits[1]->update(['is_active' => false]);

    $checkIn = $this->save->handle($this->user, CarbonImmutable::parse('2026-07-15'), [$this->habits[1]->id => true]);

    expect($checkIn->completions)->toHaveCount(2)
        ->and($checkIn->completions->pluck('habit_id'))->not->toContain($this->habits[1]->id);
});
