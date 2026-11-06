<?php

namespace App\Filament\Resources\Habits\Pages;

use App\Actions\Habits\EnsureDefaultHabits;
use App\Filament\Resources\Habits\HabitResource;
use App\Models\Habit;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageHabits extends ManageRecords
{
    protected static string $resource = HabitResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        return __('habits.resource.subtitle');
    }

    /**
     * Re-create the starter habits (only those the user doesn't have).
     */
    public function restoreDefaults(): void
    {
        /** @var User $user */
        $user = auth()->user();

        app(EnsureDefaultHabits::class)->handle($user);

        Notification::make()->success()->title(__('habits.resource.defaults_restored'))->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->using(function (array $data): Habit {
                    /** @var User $user */
                    $user = auth()->user();

                    // Ownership is never taken from the form: it is always the signed-in user.
                    $habit = new Habit($data);
                    $habit->user()->associate($user);
                    $habit->sort_order = (int) $user->habits()->max('sort_order') + 1;
                    $habit->save();

                    return $habit;
                }),
        ];
    }
}
