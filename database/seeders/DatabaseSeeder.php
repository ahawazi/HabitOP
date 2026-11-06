<?php

namespace Database\Seeders;

use App\Enums\HabitCategory;
use App\Models\DailyCheckIn;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a ready-to-login demo user (test@example.com / password) with the
     * default habits, a few custom ones and ~two months of history.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'test@example.com')->first()
            ?? User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'created_at' => Date::today()->subMonths(3),
            ]);

        $custom = [
            ['name' => 'Drink 8 glasses of water', 'category' => HabitCategory::Health, 'target_per_week' => 7, 'icon' => 'heroicon-o-beaker'],
            ['name' => 'Meditate 10 minutes', 'category' => HabitCategory::Mind, 'target_per_week' => 5, 'icon' => 'heroicon-o-sparkles'],
            ['name' => 'Deep work session', 'category' => HabitCategory::Work, 'target_per_week' => 5, 'icon' => 'heroicon-o-briefcase'],
        ];

        foreach ($custom as $index => $data) {
            $user->habits()->firstOrCreate(['name' => $data['name']], [
                'category' => $data['category']->value,
                'color' => $data['category']->defaultHex(),
                'icon' => $data['icon'],
                'target_per_week' => $data['target_per_week'],
                'sort_order' => 10 + $index,
            ]);
        }

        if ($user->dailyCheckIns()->exists()) {
            return;
        }

        $habits = $user->habits()->activeOrdered()->get();
        $today = Date::today()->toImmutable();

        foreach (range(1, 60) as $daysAgo) {
            if (random_int(1, 100) > 85) {
                continue;
            }

            $checkIn = DailyCheckIn::factory()
                ->forUser($user)
                ->on($today->subDays($daysAgo))
                ->create(['notes' => null]);

            $habits->each(fn (Habit $habit) => $checkIn->completions()->create([
                'habit_id' => $habit->id,
                'completed' => random_int(1, 100) <= ($daysAgo <= 6 ? 95 : 70),
            ]));
        }
    }
}
