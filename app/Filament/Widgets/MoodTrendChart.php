<?php

namespace App\Filament\Widgets;

use App\Actions\Habits\GetHabitProgress;
use App\Data\DailyProgress;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\On;

/**
 * Completion percentage and mood over the last 30 days.
 */
class MoodTrendChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected static bool $isDiscovered = false;

    protected ?string $maxHeight = '260px';

    public function getHeading(): string|Htmlable|null
    {
        return __('habits.charts.trend_title');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('habits.charts.trend_subtitle');
    }

    #[On('habit-progress-updated')]
    public function refreshChart(): void
    {
        $this->cachedData = null;
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $today = Date::today()->toImmutable();
        $days = app(GetHabitProgress::class)->forRange($user, $today->subDays(29), $today)->values();

        return [
            'datasets' => [
                [
                    'label' => __('habits.charts.completion'),
                    'data' => $days->map(fn (DailyProgress $day): int => $day->percentage)->all(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => __('habits.charts.mood'),
                    'data' => $days->map(fn (DailyProgress $day): ?int => $day->moodScore)->all(),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => '#8b5cf6',
                    'spanGaps' => true,
                    'tension' => 0.35,
                    'yAxisID' => 'mood',
                ],
            ],
            'labels' => $days->map(fn (DailyProgress $day): string => $day->date->translatedFormat('j M'))->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'scales' => [
                'y' => ['min' => 0, 'max' => 100, 'position' => 'left'],
                'mood' => ['min' => 0, 'max' => 10, 'position' => 'right', 'grid' => ['drawOnChartArea' => false]],
            ],
        ];
    }
}
