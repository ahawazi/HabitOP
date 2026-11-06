<?php

namespace App\Policies;

use App\Models\DailyCheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

/**
 * Check-ins are personal: a user may only ever read or write their own rows.
 */
class DailyCheckInPolicy
{
    /**
     * Determine whether the user can list their own check-ins.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the check-in.
     */
    public function view(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $this->owns($user, $dailyCheckIn);
    }

    /**
     * Determine whether the user can record a check-in for the given date.
     */
    public function create(User $user, ?CarbonImmutable $date = null): bool
    {
        return ! $this->isFuture($date);
    }

    /**
     * Determine whether the user can update the check-in.
     */
    public function update(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $this->owns($user, $dailyCheckIn) && ! $this->isFuture($dailyCheckIn->date);
    }

    /**
     * Determine whether the user can delete the check-in.
     */
    public function delete(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $this->owns($user, $dailyCheckIn);
    }

    /**
     * Determine whether the user can bulk-delete their check-ins.
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the check-in belongs to the user.
     */
    protected function owns(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $dailyCheckIn->user_id === $user->id;
    }

    /**
     * Determine whether the given date lies ahead of today.
     */
    protected function isFuture(?CarbonImmutable $date): bool
    {
        return $date !== null && $date->startOfDay()->greaterThan(Date::today()->toImmutable());
    }
}
