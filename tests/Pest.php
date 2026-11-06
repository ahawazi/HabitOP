<?php

use App\Models\DailyCheckIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Record a day on which every active habit of the user was completed.
 */
function completeDay(User $user, string $date): DailyCheckIn
{
    return DailyCheckIn::factory()->forUser($user)->on($date)->completed()->create();
}

/**
 * Record a day on which only the first N active habits were completed.
 */
function partialDay(User $user, string $date, int $completed): DailyCheckIn
{
    return DailyCheckIn::factory()->forUser($user)->on($date)->partiallyCompleted($completed)->create();
}
