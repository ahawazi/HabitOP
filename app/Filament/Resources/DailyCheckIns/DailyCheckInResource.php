<?php

namespace App\Filament\Resources\DailyCheckIns;

use App\Filament\Navigation\NavigationGroup;
use App\Filament\Pages\CheckIn;
use App\Filament\Resources\DailyCheckIns\Pages\ManageDailyCheckIns;
use App\Models\DailyCheckIn;
use App\Models\User;
use App\Support\LocalCalendar;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Read-only history of the signed-in user's check-ins. Editing happens on the
 * check-in page, so every write goes through SaveDailyCheckIn.
 */
class DailyCheckInResource extends Resource
{
    protected static ?string $model = DailyCheckIn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'history';

    public static function getModelLabel(): string
    {
        return __('habits.history.check_in');
    }

    public static function getPluralModelLabel(): string
    {
        return __('habits.history.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('habits.nav.history');
    }

    /**
     * Only ever the signed-in user's own check-ins.
     *
     * @return Builder<DailyCheckIn>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var User $user */
        $user = auth()->user();

        return DailyCheckIn::query()->ownedBy($user);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'completions as completed_count' => fn (Builder $completions) => $completions
                    ->where('completed', true)
                    ->whereHas('habit', fn (Builder $habit) => $habit->where('is_active', true)),
            ]))
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label(__('habits.history.date'))
                    ->formatStateUsing(fn (DailyCheckIn $record): string => LocalCalendar::format($record->date, __('habits.formats.long_date')))
                    ->sortable(),
                TextColumn::make('completed_count')
                    ->label(__('habits.history.completed'))
                    ->formatStateUsing(fn (int $state): string => __('habits.history.completed_value', [
                        'completed' => $state,
                        'total' => static::activeHabitCount(),
                    ]))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        static::activeHabitCount() > 0 && $state >= static::activeHabitCount() => 'success',
                        $state > 0 => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('mood_score')
                    ->label(__('habits.history.mood'))
                    ->formatStateUsing(fn (?int $state): string => $state !== null ? __('habits.check_in.mood_score_of', ['mood' => $state, 'max' => DailyCheckIn::MOOD_MAX]) : '—')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('habits.history.notes'))
                    ->limit(60)
                    ->wrap()
                    ->placeholder('—')
                    ->searchable(),
            ])
            ->filters([
                Filter::make('date')
                    ->schema([
                        static::datePicker('from')->label(__('habits.history.from')),
                        static::datePicker('until')->label(__('habits.history.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date) => $query->whereDate('date', '<=', $date))),
                Filter::make('with_notes')
                    ->label(__('habits.history.with_notes'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('notes')),
            ])
            ->recordUrl(fn (DailyCheckIn $record): string => CheckIn::getUrl(['date' => $record->date->toDateString()]))
            ->recordActions([
                Action::make('open')
                    ->label(__('habits.history.open'))
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (DailyCheckIn $record): string => CheckIn::getUrl(['date' => $record->date->toDateString()])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDailyCheckIns::route('/'),
        ];
    }

    /**
     * A date picker in the active calendar (the Jalali picker in Persian).
     */
    protected static function datePicker(string $name): DatePicker
    {
        return DatePicker::make($name)
            ->when(LocalCalendar::isJalali(), fn (DatePicker $picker): DatePicker => $picker
                ->jalali()
                ->displayFormat(__('habits.formats.day_month_year')));
    }

    /**
     * The number of active habits (the denominator for every row).
     */
    protected static function activeHabitCount(): int
    {
        /** @var User $user */
        $user = auth()->user();

        return once(fn (): int => $user->habits()->where('is_active', true)->count());
    }
}
