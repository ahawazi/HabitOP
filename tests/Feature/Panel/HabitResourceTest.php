<?php

use App\Filament\Resources\Habits\Pages\ManageHabits;
use App\Models\Habit;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('a user can create a habit that belongs to them', function () {
    Livewire::test(ManageHabits::class)
        ->callAction(CreateAction::class, data: [
            'name' => 'Drink water',
            'category' => 'health',
            'color' => '#10b981',
            'target_per_week' => 5,
            'is_active' => true,
        ])
        ->assertHasNoFormErrors();

    $habit = Habit::query()->where('name', 'Drink water')->sole();

    expect($habit->user_id)->toBe($this->user->id)
        ->and($habit->target_per_week)->toBe(5)
        ->and($habit->sort_order)->toBe(4);
});

test('a habit name is required', function () {
    Livewire::test(ManageHabits::class)
        ->callAction(CreateAction::class, data: ['name' => ''])
        ->assertHasFormErrors(['name' => 'required']);
});

test('a user can edit and delete their own habit', function () {
    $habit = $this->user->habits()->first();

    Livewire::test(ManageHabits::class)
        ->callAction(TestAction::make('edit')->table($habit), data: ['name' => 'Morning run', 'target_per_week' => 3])
        ->assertHasNoFormErrors();

    expect($habit->fresh()->name)->toBe('Morning run')
        ->and($habit->fresh()->target_per_week)->toBe(3);

    Livewire::test(ManageHabits::class)
        ->callAction(TestAction::make(DeleteAction::class)->table($habit));

    expect(Habit::find($habit->id))->toBeNull();
});

test('restoring the starter habits recreates deleted defaults', function () {
    $this->user->habits()->delete();

    Livewire::test(ManageHabits::class)->call('restoreDefaults');

    expect($this->user->habits()->count())->toBe(3);
});
