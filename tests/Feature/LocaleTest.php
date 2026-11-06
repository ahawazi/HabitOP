<?php

use App\Filament\Pages\Dashboard;
use App\Models\User;

test('the landing page renders in persian by default, right to left', function () {
    config(['app.locale' => 'fa']);

    $this->get('/')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('عادت‌هایی بساز که واقعاً بمانند');
});

test('guests can switch the language', function () {
    $this->from('/')->get('/locale/en')->assertRedirect('/');

    $this->get('/')->assertSee('dir="ltr"', false)->assertSee('Build habits that actually stick');

    $this->get('/locale/fa');

    $this->get('/')->assertSee('dir="rtl"', false);
});

test('unsupported languages are rejected', function () {
    $this->get('/locale/de')->assertNotFound();
});

test('a signed-in user\'s language choice is saved to their profile', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)->get('/locale/fa');

    expect($user->fresh()->locale)->toBe('fa');

    $this->get(Dashboard::getUrl())->assertOk()->assertSee('dir="rtl"', false)->assertSee('داشبورد');
});

test('the saved preference wins over the session', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->withSession(['locale' => 'fa'])
        ->get(Dashboard::getUrl())
        ->assertSee('dir="ltr"', false);
});
