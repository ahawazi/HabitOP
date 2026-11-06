<?php

namespace App\Enums;

/**
 * The starter habits every new user receives.
 *
 * The case value is the stable slug persisted on `habits.slug`. It never changes,
 * so translations and tests can rely on it. The English label is only used as the
 * seeded `habits.name` value; the UI renders the translated label until the user
 * renames the habit.
 */
enum DefaultHabit: string
{
    case Exercise = 'exercise';
    case Reading = 'reading';
    case Journaling = 'journaling';

    /**
     * The canonical English name persisted to the database.
     */
    public function label(): string
    {
        return match ($this) {
            self::Exercise => 'Exercise',
            self::Reading => 'Reading',
            self::Journaling => 'Journaling',
        };
    }

    /**
     * The position this habit occupies in every list and form.
     */
    public function sortOrder(): int
    {
        return match ($this) {
            self::Exercise => 1,
            self::Reading => 2,
            self::Journaling => 3,
        };
    }

    /**
     * The icon used to represent this habit in the interface.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Exercise => 'heroicon-o-bolt',
            self::Reading => 'heroicon-o-book-open',
            self::Journaling => 'heroicon-o-pencil-square',
        };
    }

    /**
     * The category this habit belongs to.
     */
    public function category(): HabitCategory
    {
        return match ($this) {
            self::Exercise => HabitCategory::Fitness,
            self::Reading => HabitCategory::Learning,
            self::Journaling => HabitCategory::Mind,
        };
    }

    /**
     * The attributes used to seed this habit for a user.
     *
     * @return array{name: string, category: string, color: string, icon: string, target_per_week: int, sort_order: int, is_active: bool}
     */
    public function toAttributes(): array
    {
        return [
            'name' => $this->label(),
            'category' => $this->category()->value,
            'color' => $this->category()->defaultHex(),
            'icon' => $this->icon(),
            'target_per_week' => 7,
            'sort_order' => $this->sortOrder(),
            'is_active' => true,
        ];
    }
}
