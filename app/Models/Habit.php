<?php

namespace App\Models;

use App\Enums\DefaultHabit;
use App\Enums\HabitCategory;
use Database\Factories\HabitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $slug
 * @property HabitCategory $category
 * @property string $color
 * @property string|null $icon
 * @property int $target_per_week
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Collection<int, HabitCompletion> $completions
 */
#[Fillable(['name', 'slug', 'category', 'color', 'icon', 'target_per_week', 'sort_order', 'is_active'])]
class Habit extends Model
{
    /** @use HasFactory<HabitFactory> */
    use HasFactory;

    /**
     * Get the user that owns the habit.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the completion records for this habit.
     *
     * @return HasMany<HabitCompletion, $this>
     */
    public function completions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }

    /**
     * Scope the query to habits owned by the given user.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeOwnedBy(Builder $query, User $user): void
    {
        $query->where('user_id', $user->id);
    }

    /**
     * Scope the query to active habits, in display order.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeActiveOrdered(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the display name.
     *
     * A seeded default habit that still carries its original English name is shown
     * translated; once the user renames it, their own name always wins.
     */
    public function displayName(): string
    {
        $default = $this->slug ? DefaultHabit::tryFrom($this->slug) : null;
        $key = "habits.names.{$this->slug}";

        if ($default && $this->name === $default->label() && Lang::has($key)) {
            return __($key);
        }

        return $this->name;
    }

    /**
     * Get the icon representing this habit.
     */
    public function iconName(): string
    {
        return $this->icon
            ?: (DefaultHabit::tryFrom((string) $this->slug)?->icon() ?? 'heroicon-o-check-circle');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'category' => HabitCategory::class,
            'target_per_week' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
