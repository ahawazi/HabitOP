<?php

use App\Models\User;

test('guests see the login and register calls to action', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('data-test="login-link"', false)
        ->assertSee('data-test="register-link"', false)
        ->assertDontSee('data-test="open-dashboard"', false);
});

test('signed-in users are offered their dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertOk()
        ->assertSee('data-test="open-dashboard"', false)
        ->assertDontSee('data-test="register-link"', false);
});
