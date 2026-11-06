<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MoodTrendChart;
use App\Filament\Widgets\ProgressStatsOverview;
use App\Filament\Widgets\WeeklyHabitMatrix;
use App\Filament\Widgets\WeeklyProgressChart;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * The user's personal dashboard — the first screen after login.
 */
class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public static function getNavigationLabel(): string
    {
        return __('habits.nav.dashboard');
    }

    public function getTitle(): string|Htmlable
    {
        return __('habits.dashboard.greeting', ['name' => auth()->user()?->name]);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('habits.dashboard.intro');
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            ProgressStatsOverview::class,
            WeeklyHabitMatrix::class,
            WeeklyProgressChart::class,
            MoodTrendChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['default' => 1, 'lg' => 2];
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkIn')
                ->label(__('habits.check_in.today_title'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->url(fn (): string => CheckIn::getUrl()),
        ];
    }
}
