<?php

namespace App\Filament\Pages;

use App\Actions\Habits\GetHabitProgress;
use App\Data\DailyProgress;
use App\Data\ProgressSummary;
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
 * A month at a glance; every day links to its check-in.
 */
class Calendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Tracker;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'calendar';

    protected string $view = 'filament.pages.calendar';

    /**
     * The displayed month, as "Y-m" in the active calendar (e.g. "1405-08" in Persian).
     */
    #[Url]
    public string $month = '';

    public static function getNavigationLabel(): string
    {
        return __('habits.nav.calendar');
    }

    public function getTitle(): string|Htmlable
    {
        return __('habits.calendar.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('habits.calendar.subtitle');
    }

    public function mount(): void
    {
        $this->month = $this->normalizeMonth($this->month);
    }

    public function previousMonth(): void
    {
        $this->month = $this->normalizeMonth(LocalCalendar::monthKey(LocalCalendar::addMonths($this->selectedMonth(), -1)));
    }

    public function nextMonth(): void
    {
        $this->month = $this->normalizeMonth(LocalCalendar::monthKey(LocalCalendar::addMonths($this->selectedMonth(), 1)));
    }

    public function currentMonth(): void
    {
        $this->month = $this->normalizeMonth(null);
    }

    public function monthLabel(): string
    {
        return LocalCalendar::format($this->selectedMonth(), __('habits.formats.month_year'));
    }

    /**
     * The months the displayed month spans in the other calendar, e.g. "Oct – Nov 2026".
     */
    public function equivalentMonthLabel(): string
    {
        $month = $this->selectedMonth();

        return LocalCalendar::monthSpan($month, LocalCalendar::endOfMonth($month), LocalCalendar::alternate());
    }

    /**
     * The grid's weeks, each a list of seven days (padding days included).
     *
     * @return array<int, array<int, array{progress: DailyProgress, outside: bool}>>
     */
    public function weeks(): array
    {
        $month = $this->selectedMonth();
        $gridStart = Week::start($month);
        $gridEnd = Week::end(LocalCalendar::endOfMonth($month));
        $progress = $this->progressFor($gridStart, $gridEnd);

        return $progress
            ->values()
            ->map(fn (DailyProgress $day): array => [
                'progress' => $day,
                'outside' => ! LocalCalendar::isSameMonth($day->date, $month),
            ])
            ->chunk(7)
            ->map(fn (Collection $week): array => $week->values()->all())
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function weekdayLabels(): array
    {
        return array_map(
            fn (CarbonImmutable $day): string => LocalCalendar::format($day, __('habits.formats.weekday_short')),
            Week::days(),
        );
    }

    public function summary(): ProgressSummary
    {
        $month = $this->selectedMonth();

        return app(GetHabitProgress::class)->summarize(
            $this->progressFor($month, LocalCalendar::endOfMonth($month)),
        );
    }

    public function isFutureMonth(): bool
    {
        return $this->selectedMonth()->greaterThan(LocalCalendar::startOfMonth(Date::today()->toImmutable()));
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
            Action::make('previousMonth')
                ->label(__('habits.calendar.previous_month'))
                ->icon(Heroicon::OutlinedChevronLeft)
                ->extraAttributes(['class' => 'rtl:[&_svg]:rotate-180'])
                ->color('gray')
                ->disabled(fn (): bool => $this->selectedMonth()->lessThanOrEqualTo($this->period()->firstMonth()))
                ->action(fn () => $this->previousMonth()),
            Action::make('currentMonth')
                ->label(__('habits.calendar.this_month'))
                ->color('gray')
                ->hidden(fn (): bool => LocalCalendar::isSameMonth($this->selectedMonth(), Date::today()->toImmutable()))
                ->action(fn () => $this->currentMonth()),
            Action::make('nextMonth')
                ->label(__('habits.calendar.next_month'))
                ->icon(Heroicon::OutlinedChevronRight)
                ->iconPosition('after')
                ->extraAttributes(['class' => 'rtl:[&_svg]:rotate-180'])
                ->color('gray')
                ->disabled(fn (): bool => $this->selectedMonth()->greaterThanOrEqualTo($this->period()->lastMonth()))
                ->action(fn () => $this->nextMonth()),
        ];
    }

    /**
     * @return Collection<string, DailyProgress>
     */
    protected function progressFor(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return app(GetHabitProgress::class)->forRange($user, $start, $end);
    }

    protected function selectedMonth(): CarbonImmutable
    {
        return LocalCalendar::parseMonthKey($this->month)
            ?? LocalCalendar::startOfMonth(Date::today()->toImmutable());
    }

    protected function period(): TrackerPeriod
    {
        /** @var User $user */
        $user = auth()->user();

        return TrackerPeriod::forUser($user);
    }

    /**
     * Parse and clamp a requested month key; anything unparsable means "this month".
     */
    protected function normalizeMonth(?string $month): string
    {
        $parsed = LocalCalendar::parseMonthKey($month) ?? Date::today()->toImmutable();

        return LocalCalendar::monthKey($this->period()->clampMonth($parsed));
    }
}
