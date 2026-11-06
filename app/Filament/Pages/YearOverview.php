<?php

namespace App\Filament\Pages;

use App\Actions\Habits\GetHabitProgress;
use App\Data\DailyProgress;
use App\Data\ProgressSummary;
use App\Data\StreakSummary;
use App\Data\TrackerPeriod;
use App\Filament\Navigation\NavigationGroup;
use App\Models\User;
use App\Support\LocalCalendar;
use App\Support\Week;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * A whole year as a heatmap, one block per month, plus the year's totals.
 */
class YearOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Tracker;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'year';

    protected string $view = 'filament.pages.year-overview';

    /**
     * The displayed year in the active calendar (e.g. 1405 in Persian).
     */
    #[Url]
    public ?int $year = null;

    /**
     * Memoised progress for the selected year.
     *
     * @var Collection<string, DailyProgress>|null
     */
    protected ?Collection $yearProgress = null;

    public static function getNavigationLabel(): string
    {
        return __('habits.nav.year_overview');
    }

    public function getTitle(): string|Htmlable
    {
        return __('habits.year.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('habits.year.subtitle', ['year' => LocalCalendar::digits($this->selectedYear())]);
    }

    public function mount(): void
    {
        $this->year = $this->period()->clampYear($this->year ?? LocalCalendar::year(Date::today()->toImmutable()));
    }

    public function updatedYear(): void
    {
        $this->year = $this->period()->clampYear((int) $this->year);
        $this->yearProgress = null;
    }

    public function selectedYear(): int
    {
        return $this->period()->clampYear($this->year ?? LocalCalendar::year(Date::today()->toImmutable()));
    }

    /**
     * Each month of the year as a grid of week columns (padding cells are null).
     *
     * @return array<int, array{label: string, weeks: array<int, array<int, DailyProgress|null>>}>
     */
    public function months(): array
    {
        $progress = $this->yearProgress();
        $months = [];

        foreach (LocalCalendar::monthsOfYear($this->selectedYear()) as $month) {
            $end = LocalCalendar::endOfMonth($month);
            $cells = array_fill(0, (int) Week::start($month)->diffInDays($month), null);

            for ($day = $month; $day->lessThanOrEqualTo($end); $day = $day->addDay()) {
                $cells[] = $progress->get($day->toDateString());
            }

            $months[] = [
                'label' => LocalCalendar::format($month, __('habits.formats.month')),
                'weeks' => array_chunk($cells, 7),
            ];
        }

        return $months;
    }

    /**
     * @return array<int, string>
     */
    public function weekdayLabels(): array
    {
        return array_map(
            fn (CarbonImmutable $day): string => mb_substr(LocalCalendar::format($day, __('habits.formats.weekday_short')), 0, 1),
            Week::days(),
        );
    }

    public function summary(): ProgressSummary
    {
        return app(GetHabitProgress::class)->summarize($this->yearProgress());
    }

    public function streaks(): StreakSummary
    {
        /** @var User $user */
        $user = auth()->user();

        return app(GetHabitProgress::class)->streaks($user);
    }

    public function dayUrl(DailyProgress $day): ?string
    {
        return $this->period()->contains($day->date)
            ? CheckIn::getUrl(['date' => $day->date->toDateString()])
            : null;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('previousYear')
                ->label(fn (): string => LocalCalendar::digits($this->selectedYear() - 1))
                ->icon(Heroicon::OutlinedChevronLeft)
                ->extraAttributes(['class' => 'rtl:[&_svg]:rotate-180'])
                ->color('gray')
                ->disabled(fn (): bool => $this->selectedYear() <= LocalCalendar::year($this->period()->start))
                ->action(function (): void {
                    $this->year = $this->selectedYear() - 1;
                    $this->updatedYear();
                }),
            Action::make('nextYear')
                ->label(fn (): string => LocalCalendar::digits($this->selectedYear() + 1))
                ->icon(Heroicon::OutlinedChevronRight)
                ->iconPosition('after')
                ->extraAttributes(['class' => 'rtl:[&_svg]:rotate-180'])
                ->color('gray')
                ->disabled(fn (): bool => $this->selectedYear() >= LocalCalendar::year($this->period()->end))
                ->action(function (): void {
                    $this->year = $this->selectedYear() + 1;
                    $this->updatedYear();
                }),
        ];
    }

    /**
     * @return Collection<string, DailyProgress>
     */
    protected function yearProgress(): Collection
    {
        /** @var User $user */
        $user = auth()->user();
        $start = LocalCalendar::yearStart($this->selectedYear());

        return $this->yearProgress ??= app(GetHabitProgress::class)->forRange($user, $start, LocalCalendar::endOfYear($start));
    }

    protected function period(): TrackerPeriod
    {
        /** @var User $user */
        $user = auth()->user();

        return TrackerPeriod::forUser($user);
    }
}
