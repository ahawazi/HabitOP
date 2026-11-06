<?php

namespace App\Filament\Pages;

use App\Actions\Habits\GetHabitProgress;
use App\Actions\Habits\SaveDailyCheckIn;
use App\Data\DailyProgress;
use App\Data\TrackerPeriod;
use App\Filament\Navigation\NavigationGroup;
use App\Filament\Resources\Habits\HabitResource;
use App\Models\DailyCheckIn;
use App\Models\Habit;
use App\Models\User;
use App\Support\LocalCalendar;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Throwable;
use UnitEnum;

/**
 * The daily check-in for a single selected date: tick habits, record mood and
 * add a note. The date lives in the query string so any day can be linked to
 * from the weekly matrix, the calendar and the year overview.
 *
 * @property-read Schema $form
 */
class CheckIn extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Tracker;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'check-in';

    protected string $view = 'filament.pages.check-in';

    /**
     * The selected date, as "Y-m-d".
     */
    #[Url]
    public string $date = '';

    /**
     * The form state.
     *
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('habits.nav.check_in');
    }

    public function getTitle(): string|Htmlable
    {
        return $this->isToday() ? __('habits.check_in.today_title') : __('habits.check_in.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('habits.calendar.with_equivalent', [
            'date' => LocalCalendar::format($this->selectedDate(), __('habits.formats.long_date')),
            'equivalent' => LocalCalendar::format($this->selectedDate(), __('habits.formats.day_month_year'), LocalCalendar::alternate()),
        ]);
    }

    public function mount(): void
    {
        $this->date = $this->normalizeDate($this->date);
        $this->fillForm();
    }

    public function form(Schema $schema): Schema
    {
        $habits = $this->habits();

        return $schema
            ->statePath('data')
            ->disabled(fn (): bool => ! $this->isRecordable())
            ->components([
                Section::make(__('habits.check_in.habits_label'))
                    ->icon(Heroicon::OutlinedListBullet)
                    ->schema([
                        CheckboxList::make('completed')
                            ->hiddenLabel()
                            ->options($habits->mapWithKeys(fn (Habit $habit): array => [
                                $habit->id => $this->habitLabel($habit),
                            ])->all())
                            ->descriptions($habits->mapWithKeys(fn (Habit $habit): array => [
                                $habit->id => $habit->category->label(),
                            ])->all())
                            ->allowHtml()
                            ->bulkToggleable()
                            ->columns(['default' => 1, 'md' => 2]),
                    ]),
                Section::make(__('habits.check_in.mood_label'))
                    ->icon(Heroicon::OutlinedFaceSmile)
                    ->description(__('habits.check_in.mood_description'))
                    ->schema([
                        ToggleButtons::make('mood_score')
                            ->hiddenLabel()
                            ->options(array_combine(
                                range(DailyCheckIn::MOOD_MIN, DailyCheckIn::MOOD_MAX),
                                array_map('strval', range(DailyCheckIn::MOOD_MIN, DailyCheckIn::MOOD_MAX)),
                            ))
                            ->colors(fn (): array => $this->moodColors())
                            ->inline()
                            ->hintAction(
                                Action::make('clearMood')
                                    ->label(__('habits.check_in.mood_clear'))
                                    ->icon(Heroicon::OutlinedXMark)
                                    ->link()
                                    ->action(fn (Set $set) => $set('mood_score', null)),
                            ),
                    ]),
                Section::make(__('habits.check_in.notes_label'))
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->placeholder(__('habits.check_in.notes_placeholder'))
                            ->rows(4)
                            ->maxLength(2000),
                    ]),
            ]);
    }

    /**
     * Save the check-in for the selected date.
     */
    public function save(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $state = $this->form->getState();

        $completed = array_map('intval', (array) ($state['completed'] ?? []));
        $completions = $this->habits()->mapWithKeys(fn (Habit $habit): array => [
            $habit->id => in_array($habit->id, $completed, true),
        ])->all();

        try {
            app(SaveDailyCheckIn::class)->handle(
                $user,
                $this->selectedDate(),
                $completions,
                filled($state['mood_score'] ?? null) ? (int) $state['mood_score'] : null,
                $state['notes'] ?? null,
            );
        } catch (ValidationException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();

            return;
        }

        Notification::make()->success()->title(__('habits.check_in.saved'))->send();
    }

    public function previousDay(): void
    {
        $this->goTo($this->selectedDate()->subDay());
    }

    public function nextDay(): void
    {
        $this->goTo($this->selectedDate()->addDay());
    }

    public function goToToday(): void
    {
        $this->goTo(Date::today()->toImmutable());
    }

    /**
     * Get the progress of the selected day (as currently saved).
     */
    public function progress(): DailyProgress
    {
        /** @var User $user */
        $user = auth()->user();

        return app(GetHabitProgress::class)->forDate($user, $this->selectedDate());
    }

    public function isToday(): bool
    {
        return $this->selectedDate()->isSameDay(Date::today()->toImmutable());
    }

    public function isFuture(): bool
    {
        return $this->selectedDate()->greaterThan(Date::today()->toImmutable());
    }

    public function isRecordable(): bool
    {
        return $this->period()->isRecordable($this->selectedDate());
    }

    public function hasHabits(): bool
    {
        return $this->habits()->isNotEmpty();
    }

    public function habitsUrl(): string
    {
        return HabitResource::getUrl('index');
    }

    public function calendarUrl(): string
    {
        return Calendar::getUrl(['month' => LocalCalendar::monthKey($this->selectedDate())]);
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('previousDay')
                ->label(__('habits.check_in.previous_day'))
                ->icon(Heroicon::OutlinedChevronLeft)
                ->extraAttributes(['class' => 'rtl:[&_svg]:rotate-180'])
                ->color('gray')
                ->disabled(fn (): bool => $this->selectedDate()->subDay()->lessThan($this->period()->start))
                ->action(fn () => $this->previousDay()),
            Action::make('goToToday')
                ->label(__('habits.check_in.go_to_today'))
                ->color('gray')
                ->hidden(fn (): bool => $this->isToday())
                ->action(fn () => $this->goToToday()),
            Action::make('nextDay')
                ->label(__('habits.check_in.next_day'))
                ->icon(Heroicon::OutlinedChevronRight)
                ->iconPosition('after')
                ->extraAttributes(['class' => 'rtl:[&_svg]:rotate-180'])
                ->color('gray')
                ->disabled(fn (): bool => $this->selectedDate()->addDay()->greaterThan($this->period()->end))
                ->action(fn () => $this->nextDay()),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('habits.check_in.save'))
                ->icon(Heroicon::OutlinedCheck)
                ->submit('save')
                ->disabled(fn (): bool => ! $this->isRecordable() || ! $this->hasHabits())
                ->keyBindings(['mod+s']),
        ];
    }

    protected function goTo(CarbonImmutable $date): void
    {
        $this->date = $this->normalizeDate($date->toDateString());
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $checkIn = DailyCheckIn::query()
            ->ownedBy($user)
            ->whereDate('date', $this->date)
            ->with('completions')
            ->first();

        $this->form->fill([
            'completed' => $checkIn
                ? $checkIn->completions->where('completed', true)->pluck('habit_id')->map(fn ($id): string => (string) $id)->values()->all()
                : [],
            'mood_score' => $checkIn?->mood_score,
            'notes' => $checkIn?->notes,
        ]);
    }

    /**
     * @return Collection<int, Habit>
     */
    protected function habits(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        return app(GetHabitProgress::class)->habits($user);
    }

    protected function habitLabel(Habit $habit): HtmlString
    {
        return new HtmlString(sprintf(
            '<span class="inline-flex items-center gap-2"><span class="inline-block size-2.5 rounded-full" style="background-color:%s"></span>%s</span>',
            e($habit->color),
            e($habit->displayName()),
        ));
    }

    /**
     * Red for a bad day, amber for so-so, green for a good one.
     *
     * @return array<int, string>
     */
    protected function moodColors(): array
    {
        $colors = [];

        foreach (range(DailyCheckIn::MOOD_MIN, DailyCheckIn::MOOD_MAX) as $score) {
            $colors[$score] = match (true) {
                $score <= 3 => 'danger',
                $score <= 6 => 'warning',
                default => 'success',
            };
        }

        return $colors;
    }

    protected function selectedDate(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->date ?: Date::today()->toDateString())->startOfDay();
    }

    protected function period(): TrackerPeriod
    {
        /** @var User $user */
        $user = auth()->user();

        return TrackerPeriod::forUser($user);
    }

    /**
     * Parse and clamp a requested date; anything unparsable means "today".
     */
    protected function normalizeDate(?string $date): string
    {
        try {
            $parsed = filled($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)
                ? CarbonImmutable::parse($date)
                : Date::today()->toImmutable();
        } catch (Throwable) {
            $parsed = Date::today()->toImmutable();
        }

        return $this->period()->clampDate($parsed)->toDateString();
    }
}
