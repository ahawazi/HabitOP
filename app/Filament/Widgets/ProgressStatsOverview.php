<?php

namespace App\Filament\Widgets;

use App\Actions\Habits\GetHabitProgress;
use App\Models\DailyCheckIn;
use App\Models\User;
use App\Support\Week;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\On;

/**
 * Headline numbers: weekly/monthly completion, weekly goals, streaks and mood.
 */
class ProgressStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    /**
     * @return int|array<string, int>|null
     */
    protected function getColumns(): int|array|null
    {
        return ['default' => 2, 'md' => 3, 'xl' => 6];
    }

    /**
     * Re-render when a habit is ticked elsewhere on the dashboard.
     */
    #[On('habit-progress-updated')]
    public function refreshStats(): void
    {
        $this->cachedStats = null;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $progress = app(GetHabitProgress::class);
        $today = Date::today()->toImmutable();

        $weekDays = $progress->forRange($user, Week::start($today), Week::end($today));
        $week = $progress->summarize($weekDays);
        $month = $progress->summarize($progress->forRange($user, $today->startOfMonth(), $today->endOfMonth()->startOfDay()));
        $streaks = $progress->streaks($user);
        $goalRate = $progress->weeklyGoalRate($progress->weeklyHabitStats($user, $today));

        return [
            Stat::make(__('habits.dashboard.this_week'), $week->percentage.'%')
                ->description(__('habits.dashboard.completed_days_elapsed', ['completed' => $week->completedDays, 'total' => $week->totalDays]))
                ->chart($weekDays->reject->isFuture->map->percentage->values()->all())
                ->color('primary')
                ->icon(Heroicon::OutlinedCalendarDays),
            Stat::make(__('habits.dashboard.this_month'), $month->percentage.'%')
                ->description(__('habits.dashboard.completed_days_elapsed', ['completed' => $month->completedDays, 'total' => $month->totalDays]))
                ->icon(Heroicon::OutlinedCalendar),
            Stat::make(__('habits.dashboard.weekly_goals'), $goalRate.'%')
                ->description(__('habits.dashboard.weekly_goals_hint'))
                ->color($goalRate >= 100 ? 'success' : 'info')
                ->icon(Heroicon::OutlinedFlag),
            Stat::make(__('habits.dashboard.current_streak'), trans_choice('habits.streak.days', $streaks->current))
                ->description(__('habits.streak.rule_short'))
                ->color($streaks->current > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedFire),
            Stat::make(__('habits.dashboard.best_streak'), trans_choice('habits.streak.days', $streaks->best))
                ->icon(Heroicon::OutlinedTrophy),
            Stat::make(__('habits.dashboard.average_mood'), $month->averageMood !== null ? __('habits.check_in.mood_score_of', ['mood' => $month->averageMood, 'max' => DailyCheckIn::MOOD_MAX]) : '—')
                ->description(__('habits.dashboard.average_mood_hint'))
                ->icon(Heroicon::OutlinedFaceSmile),
        ];
    }
}
