<x-filament-panels::page>
    @php($progress = $this->progress())

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if ($this->isFuture())
                <x-filament::callout icon="heroicon-o-clock" color="info" data-test="future-notice">
                    <x-slot name="description">{{ __('habits.check_in.future_notice') }}</x-slot>
                </x-filament::callout>
            @endif

            @unless ($this->hasHabits())
                <x-filament::empty-state icon="heroicon-o-sparkles" :heading="__('habits.empty.no_habits')">
                    <x-slot name="footer">
                        <x-filament::button tag="a" :href="$this->habitsUrl()" icon="heroicon-o-plus">{{ __('habits.empty.create') }}</x-filament::button>
                    </x-slot>
                </x-filament::empty-state>
            @else
                <form wire:submit="save" class="space-y-6">
                    {{ $this->form }}

                    <div class="flex flex-wrap gap-3">
                        @foreach ($this->getFormActions() as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </form>
            @endunless
        </div>

        <aside class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">{{ __('habits.check_in.progress') }}</x-slot>

                <div class="flex items-center gap-4">
                    <div class="relative size-20 shrink-0">
                        <svg viewBox="0 0 36 36" class="size-20 -rotate-90">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.5" class="stroke-gray-100 dark:stroke-white/10" />
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.5" stroke-linecap="round"
                                class="stroke-primary-500 transition-all duration-500"
                                stroke-dasharray="{{ $progress->percentage }}, 100" />
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-lg font-bold tabular-nums text-gray-950 dark:text-white" data-test="day-percentage">{{ $progress->percentage }}%</span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="font-medium text-gray-950 dark:text-white">{{ __('habits.check_in.habits_completed', ['completed' => $progress->completedCount, 'total' => $progress->totalCount]) }}</p>
                        @if ($progress->moodScore !== null)
                            <p class="text-gray-500 dark:text-gray-400">{{ __('habits.check_in.mood_value', ['mood' => $progress->moodScore, 'max' => \App\Models\DailyCheckIn::MOOD_MAX]) }}</p>
                        @endif
                        @if ($progress->isComplete())
                            <x-filament::badge color="success" icon="heroicon-m-fire">{{ __('habits.check_in.all_done') }}</x-filament::badge>
                        @endif
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">{{ __('habits.check_in.progress_hint') }}</p>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">{{ __('habits.check_in.shortcuts') }}</x-slot>
                <div class="flex flex-col gap-2 text-sm">
                    <x-filament::link :href="$this->calendarUrl()" icon="heroicon-o-calendar-days">{{ __('habits.dashboard.view_calendar') }}</x-filament::link>
                    <x-filament::link :href="\App\Filament\Pages\YearOverview::getUrl()" icon="heroicon-o-squares-2x2">{{ __('habits.dashboard.view_year') }}</x-filament::link>
                    <x-filament::link :href="$this->habitsUrl()" icon="heroicon-o-adjustments-horizontal">{{ __('habits.dashboard.manage_habits') }}</x-filament::link>
                </div>
            </x-filament::section>
        </aside>
    </div>
</x-filament-panels::page>
