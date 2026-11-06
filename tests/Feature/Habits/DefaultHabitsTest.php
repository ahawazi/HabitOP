<?php

use App\Actions\Habits\EnsureDefaultHabits;
use App\Enums\DefaultHabit;
use App\Models\User;
use Illuminate\Support\Facades\App;

test('new users get the three starter habits', function () {
    $user = User::factory()->create();

    expect($user->habits()->orderBy('sort_order')->pluck('slug')->all())
        ->toBe(array_map(fn (DefaultHabit $habit) => $habit->value, DefaultHabit::cases()));
});

test('ensuring defaults is idempotent and keeps customisations', function () {
    $user = User::factory()->create();
    $user->habits()->where('slug', 'reading')->update(['name' => 'Read a book', 'is_active' => false]);

    app(EnsureDefaultHabits::class)->handle($user);
    app(EnsureDefaultHabits::class)->handle($user);

    expect($user->habits()->count())->toBe(3)
        ->and($user->habits()->where('slug', 'reading')->value('name'))->toBe('Read a book');
});

test('the sync command only seeds users without any habits', function () {
    $empty = User::factory()->withoutHabits()->create();
    $custom = User::factory()->withoutHabits()->create();
    $custom->habits()->create(['name' => 'Only mine']);

    $this->artisan('habits:sync-defaults')->assertSuccessful();

    expect($empty->habits()->count())->toBe(3)
        ->and($custom->habits()->count())->toBe(1);
});

test('default habit names are translated until the user renames them', function () {
    $user = User::factory()->create();
    $exercise = $user->habits()->where('slug', 'exercise')->first();

    App::setLocale('fa');
    expect($exercise->displayName())->toBe('ورزش');

    $exercise->update(['name' => 'Morning run']);
    expect($exercise->displayName())->toBe('Morning run');
});
