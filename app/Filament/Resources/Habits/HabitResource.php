<?php

namespace App\Filament\Resources\Habits;

use App\Enums\HabitCategory;
use App\Filament\Navigation\NavigationGroup;
use App\Filament\Resources\Habits\Pages\ManageHabits;
use App\Models\Habit;
use App\Models\User;
use App\Support\Week;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * CRUD for the signed-in user's own habits (HabitFlow's habit manager).
 */
class HabitResource extends Resource
{
    protected static ?string $model = Habit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Icons a user can pick for a habit.
     *
     * @var array<int, string>
     */
    public const array ICONS = [
        'heroicon-o-check-circle', 'heroicon-o-bolt', 'heroicon-o-book-open', 'heroicon-o-pencil-square',
        'heroicon-o-heart', 'heroicon-o-beaker', 'heroicon-o-sparkles', 'heroicon-o-briefcase',
        'heroicon-o-academic-cap', 'heroicon-o-musical-note', 'heroicon-o-moon', 'heroicon-o-sun',
        'heroicon-o-fire', 'heroicon-o-language', 'heroicon-o-code-bracket', 'heroicon-o-currency-dollar',
        'heroicon-o-face-smile', 'heroicon-o-globe-alt', 'heroicon-o-home', 'heroicon-o-trophy',
    ];

    public static function getModelLabel(): string
    {
        return __('habits.resource.habit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('habits.resource.habits');
    }

    public static function getNavigationLabel(): string
    {
        return __('habits.nav.habits');
    }

    /**
     * Only ever the signed-in user's own habits.
     *
     * @return Builder<Habit>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var User $user */
        $user = auth()->user();

        return Habit::query()->ownedBy($user);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('habits.resource.fields.name'))
                    ->placeholder(__('habits.resource.fields.name_placeholder'))
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Grid::make(2)->schema([
                    Select::make('category')
                        ->label(__('habits.resource.fields.category'))
                        ->options(HabitCategory::options())
                        ->default(HabitCategory::Health->value)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (?string $state, Get $get, Set $set, string $operation): void {
                            $category = HabitCategory::tryFrom((string) $state);

                            if ($operation === 'create' && $category) {
                                $set('color', $category->defaultHex());
                            }
                        }),
                    ColorPicker::make('color')
                        ->label(__('habits.resource.fields.color'))
                        ->default(HabitCategory::Health->defaultHex())
                        ->regex('/^#[0-9a-fA-F]{6}$/')
                        ->required(),
                ]),
                ToggleButtons::make('target_per_week')
                    ->label(__('habits.resource.fields.target_per_week'))
                    ->helperText(__('habits.resource.fields.target_help'))
                    ->options(array_combine(range(1, 7), array_map('strval', range(1, 7))))
                    ->default(7)
                    ->inline()
                    ->required(),
                Select::make('icon')
                    ->label(__('habits.resource.fields.icon'))
                    ->options(array_combine(self::ICONS, array_map(
                        fn (string $icon): string => str($icon)->after('heroicon-o-')->replace('-', ' ')->title()->toString(),
                        self::ICONS,
                    )))
                    ->searchable()
                    ->native(false),
                Toggle::make('is_active')
                    ->label(__('habits.resource.fields.is_active'))
                    ->helperText(__('habits.resource.fields.is_active_help'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        $weekStart = Week::start();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'completions as done_this_week' => fn (Builder $completions) => $completions
                    ->where('completed', true)
                    ->whereHas('dailyCheckIn', fn (Builder $checkIn) => $checkIn
                        ->whereDate('date', '>=', $weekStart->toDateString())
                        ->whereDate('date', '<=', $weekStart->addDays(6)->toDateString())),
            ]))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->width('1%'),
                IconColumn::make('icon')
                    ->label('')
                    ->icon(fn (Habit $record): string => $record->iconName())
                    ->color('gray')
                    ->width('1%'),
                TextColumn::make('name')
                    ->label(__('habits.resource.fields.name'))
                    ->formatStateUsing(fn (Habit $record): string => $record->displayName())
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('category')
                    ->label(__('habits.resource.fields.category'))
                    ->badge()
                    ->formatStateUsing(fn (HabitCategory $state): string => $state->label())
                    ->color(fn (HabitCategory $state): string => $state->color()),
                TextColumn::make('done_this_week')
                    ->label(__('habits.resource.fields.this_week'))
                    ->formatStateUsing(fn (Habit $record, int $state): string => __('habits.week.target_progress', [
                        'done' => $state,
                        'target' => $record->target_per_week,
                    ]))
                    ->badge()
                    ->color(fn (Habit $record, int $state): string => $state >= $record->target_per_week ? 'success' : 'gray'),
                ToggleColumn::make('is_active')
                    ->label(__('habits.resource.fields.is_active')),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('habits.resource.fields.category'))
                    ->options(HabitCategory::options()),
                TernaryFilter::make('is_active')
                    ->label(__('habits.resource.fields.is_active')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('habits.empty.no_habits'))
            ->emptyStateActions([
                Action::make('restoreDefaults')
                    ->label(__('habits.resource.restore_defaults'))
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->action(fn (ManageHabits $livewire) => $livewire->restoreDefaults()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHabits::route('/'),
        ];
    }
}
