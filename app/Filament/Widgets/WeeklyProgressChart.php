<?php

namespace App\Filament\Widgets;

use App\Actions\Habits\GetHabitProgress;
use App\Data\DailyProgress;
use App\Models\User;
use App\Support\Week;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On;

/**
 * Habits completed per day this week (the HabitFlow chart).
 */
class WeeklyProgressChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static bool $isDiscovered = false;

    protected ?string $maxHeight = '260px';

    public function getHeading(): string|Htmlable|null
    {
        return __('habits.charts.weekly_title');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('habits.charts.weekly_subtitle');
    }

    #[On('habit-progress-updated')]
    public function refreshChart(): void
    {
        $this->cachedData = null;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $days = app(GetHabitProgress::class)->forRange($user, Week::start(), Week::end())->values();

        return [
            'datasets' => [
                [
                    'label' => __('habits.charts.completed_habits'),
                    'data' => $days->map(fn (DailyProgress $day): int => $day->completedCount)->all(),
                    'backgroundColor' => $days->map(fn (DailyProgress $day): string => $day->isToday ? '#06b6d4' : '#10b981')->all(),
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $days->map(fn (DailyProgress $day): string => $day->date->translatedFormat('D'))->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
