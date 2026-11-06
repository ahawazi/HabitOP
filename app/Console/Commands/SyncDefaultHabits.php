<?php

namespace App\Console\Commands;

use App\Actions\Habits\EnsureDefaultHabits;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

#[Signature('habits:sync-defaults')]
#[Description('Give the default starter habits to every user who has no habits yet (safe to run repeatedly)')]
class SyncDefaultHabits extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(EnsureDefaultHabits $ensureDefaultHabits): int
    {
        $users = 0;

        User::query()->whereDoesntHave('habits')->chunkById(100, function (Collection $chunk) use ($ensureDefaultHabits, &$users): void {
            /** @var User $user */
            foreach ($chunk as $user) {
                $ensureDefaultHabits->handle($user);
                $users++;
            }
        });

        $this->components->info("Default habits synced for {$users} user(s).");

        return self::SUCCESS;
    }
}
