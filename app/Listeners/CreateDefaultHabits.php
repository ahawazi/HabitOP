<?php

namespace App\Listeners;

use App\Actions\Habits\EnsureDefaultHabits;
use App\Models\User;
use Filament\Auth\Events\Registered as PanelRegistered;
use Illuminate\Auth\Events\Registered;

/**
 * Give every newly registered user the starter habits, so the dashboard is never
 * empty on first login.
 */
class CreateDefaultHabits
{
    public function __construct(private EnsureDefaultHabits $ensureDefaultHabits)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered|PanelRegistered $event): void
    {
        $user = $event instanceof PanelRegistered ? $event->getUser() : $event->user;

        if ($user instanceof User) {
            $this->ensureDefaultHabits->handle($user);
        }
    }
}
