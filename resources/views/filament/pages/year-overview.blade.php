<x-filament-panels::page>
    @php
        $summary = $this->summary();
        $streaks = $this->streaks();
    @endphp

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach ([
            ['label' => __('habits.year.overall_completion'), 'value' => $summary->percentage.'%', 'icon' => 'heroicon-o-chart-pie', 'test' => 'overall'],
            ['label' => __('habits.year.completed_days'), 'value' => $summary->completedDays, 'icon' => 'heroicon-o-check-badge', 'test' => 'completed-days'],
            ['label' => __('habits.year.current_streak'), 'value' => trans_choice('habits.streak.days', $streaks->current), 'icon' => 'heroicon-o-fire', 'test' => 'current-streak'],
            ['label' => __('habits.year.best_streak'), 'value' => trans_choice('habits.streak.days', $streaks->best), 'icon' => 'heroicon-o-trophy', 'test' => 'best-streak'],
        ] as $stat)
            <x-filament::section compact>
                <div class="flex items-center gap-3" data-test="year-stat-{{ $stat['test'] }}">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                        <x-filament::icon :icon="$stat['icon']" class="size-5" />
                    </span>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
                        <div class="text-lg font-semibold text-gray-950 dark:text-white">{{ $stat['value'] }}</div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    <x-filament::section>
        <div class="grid grid-cols-2 gap-x-6 gap-y-8 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($this->months() as $month)
                <div>
                    <h3 class="mb-2 text-sm font-semibold text-gray-950 dark:text-white">{{ $month['label'] }}</h3>
                    <div class="inline-grid grid-cols-7 gap-1">
                        @foreach ($this->weekdayLabels() as $label)
                            <span class="text-center text-[10px] text-gray-400">{{ $label }}</span>
                        @endforeach
                        @foreach ($month['weeks'] as $week)
                            @foreach ($week as $day)
                                @if ($day)
                                    <x-habits.day-tile variant="heatmap" :progress="$day" :href="$this->dayUrl($day)" />
                                @else
                                    <span class="size-3.5 sm:size-4"></span>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex flex-col gap-2">
            <x-habits.progress-legend />
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('habits.streak.rule') }}</p>
        </div>
    </x-filament::section>
</x-filament-panels::page>
