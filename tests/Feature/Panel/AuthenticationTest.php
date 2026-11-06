<?php

use App\Filament\Auth\Register;
use App\Filament\Pages\Dashboard;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Livewire\Livewire;

test('guests are sent to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/dashboard/login');
});

test('the login and register pages render', function () {
    $this->get('/dashboard/login')->assertOk();
    $this->get('/dashboard/register')->assertOk();
});

test('friendly /login and /register aliases redirect into the panel', function () {
    $this->get('/login')->assertRedirect('/dashboard/login');
    $this->get('/register')->assertRedirect('/dashboard/register');
});

test('users can log in and land on their dashboard', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->fillForm(['email' => $user->email, 'password' => 'password'])
        ->call('authenticate')
        ->assertRedirect(Dashboard::getUrl());

    $this->assertAuthenticatedAs($user);
});

test('registration creates the user with starter habits and the current language', function () {
    app()->setLocale('fa');

    Livewire::test(Register::class)
        ->fillForm([
            'name' => 'Sara',
            'email' => 'sara@example.com',
            'password' => 'secret-password',
            'passwordConfirmation' => 'secret-password',
        ])
        ->call('register')
        ->assertHasNoFormErrors()
        ->assertRedirect(Dashboard::getUrl());

    $user = User::query()->where('email', 'sara@example.com')->sole();

    expect($user->locale)->toBe('fa')
        ->and($user->habits()->count())->toBe(3);

    $this->assertAuthenticatedAs($user);
});
