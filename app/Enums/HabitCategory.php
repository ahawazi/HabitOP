<?php

namespace App\Enums;

/**
 * The categories a habit can be filed under (from HabitFlow).
 *
 * The case value is persisted on `habits.category`; the label is always translated.
 */
enum HabitCategory: string
{
    case Health = 'health';
    case Fitness = 'fitness';
    case Mind = 'mind';
    case Work = 'work';
    case Learning = 'learning';
    case Other = 'other';

    /**
     * The translated label for this category.
     */
    public function label(): string
    {
        return __("habits.categories.{$this->value}");
    }

    /**
     * The Filament colour used for this category's badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Health => 'success',
            self::Fitness => 'warning',
            self::Mind => 'info',
            self::Work => 'primary',
            self::Learning => 'danger',
            self::Other => 'gray',
        };
    }

    /**
     * The suggested hex colour for a new habit in this category.
     */
    public function defaultHex(): string
    {
        return match ($this) {
            self::Health => '#10b981',
            self::Fitness => '#f59e0b',
            self::Mind => '#8b5cf6',
            self::Work => '#3b82f6',
            self::Learning => '#ec4899',
            self::Other => '#64748b',
        };
    }

    /**
     * Get every category as a value => translated label map, for select inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
