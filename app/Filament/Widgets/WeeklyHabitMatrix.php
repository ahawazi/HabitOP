<?php

namespace App\Filament\Widgets;

use App\Actions\Habits\GetHabitProgress;
use App\Actions\Habits\ToggleHabitCompletion;
use App\Data\TrackerPeriod;
use App\Data\WeeklyHabitStat;
use App\Filament\Pages\CheckIn;
use App\Filament\Resources\Habits\HabitResource;
use App\Models\User;
use App\Support\LocalCalendar;
use App\Support\Week;
use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;

/**
 * HabitFlow's habit × weekday matrix: one tap ticks a habit for a day, and each
 * row shows progress towards that habit's weekly target.
 */
class WeeklyHabitMatrix extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.weekly-habit-matrix';

    /**
     * Any date inside the displayed week, as "Y-m-d".
     */
    public string $week = '';

    public function mount(): void
    {
        $this->week = Week::start()->toDateString();
    }

    /**
     * Show the previous week.
     */
    public function previousWeek(): void
    {
        $this->week = $this->weekStart()->subWeek()->toDateString();
        unset($this->stats, $this->days);
    }

    /**
     * Show the next week (never beyond the current one).
     */
    public function nextWeek(): void
    {
        if ($this->isCurrentWeek()) {
            return;
        }

        $this->week = $this->weekStart()->addWeek()->toDateString();
        unset($this->stats, $this->days);
    }

    /**
     * Jump back to the current week.
     */
    public function currentWeek(): void
    {
        $this->week = Week::start()->toDateString();
        unset($this->stats, $this->days);
    }

    /**
     * Toggle a habit for a single day.
     */
    public function toggle(int $habitId, string $date): void
    {
        /** @var User $user */
        $user = auth()->user();

        // Resolved through the owner's relation: someone else's habit ID is a 404.
        $habit = $user->habits()->findOrFail($habitId);

        try {
            app(ToggleHabitCompletion::class)->handle($user, $habit, CarbonImmutable::parse($date));
        } catch (ValidationException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();

            return;
        }

        unset($this->stats);
        $this->dispatch('habit-progress-updated');
    }

    /**
     * @return Collection<int, WeeklyHabitStat>
     */
    #[Computed]
    public function stats(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return app(GetHabitProgress::class)->weeklyHabitStats($user, $this->weekStart());
    }

    /**
     * @return array<int, array{date: string, label: string, number: string, isToday: bool, isRecordable: bool}>
     */
    #[Computed]
    public function days(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $period = TrackerPeriod::forUser($user);
        $today = Date::today()->toImmutable();

        return array_map(fn (CarbonImmutable $day): array => [
            'date' => $day->toDateString(),
            'label' => LocalCalendar::format($day, __('habits.formats.weekday_short')),
            'number' => LocalCalendar::format($day, __('habits.formats.day_month')),
            'isToday' => $day->isSameDay($today),
            'isRecordable' => $period->isRecordable($day),
        ], Week::days($this->weekStart()));
    }

    public function isCurrentWeek(): bool
    {
        return $this->weekStart()->isSameDay(Week::start());
    }

    public function weekLabel(): string
    {
        $start = $this->weekStart();

        return LocalCalendar::format($start, __('habits.formats.day_month'))
            .' – '.LocalCalendar::format($start->addDays(6), __('habits.formats.day_month_year'));
    }

    public function checkInUrl(string $date): string
    {
        return CheckIn::getUrl(['date' => $date]);
    }

    public function habitsUrl(): string
    {
        return HabitResource::getUrl('index');
    }

    protected function weekStart(): CarbonImmutable
    {
        return Week::start(CarbonImmutable::parse($this->week ?: Date::today()->toDateString()));
    }
}
