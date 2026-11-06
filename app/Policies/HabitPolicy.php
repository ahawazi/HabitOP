<?php

namespace App\Policies;

use App\Models\Habit;
use App\Models\User;

/**
 * Every user is the administrator of their own habits — and only of their own.
 */
class HabitPolicy
{
    /**
     * Determine whether the user can list their habits.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the habit.
     */
    public function view(User $user, Habit $habit): bool
    {
        return $this->owns($user, $habit);
    }

    /**
     * Determine whether the user can create habits.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the habit.
     */
    public function update(User $user, Habit $habit): bool
    {
        return $this->owns($user, $habit);
    }

    /**
     * Determine whether the user can delete the habit.
     */
    public function delete(User $user, Habit $habit): bool
    {
        return $this->owns($user, $habit);
    }

    /**
     * Determine whether the user can bulk-delete their habits.
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can reorder their habits.
     */
    public function reorder(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the habit belongs to the user.
     */
    protected function owns(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }
}
