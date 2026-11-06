<x-filament-panels::page>
    @php($summary = $this->summary())

    <x-filament::section>
        <x-slot name="heading">
            <span data-test="calendar-month">{{ $this->monthLabel() }}</span>
        </x-slot>
        <x-slot name="description">
            @if ($this->isFutureMonth())
                {{ __('habits.calendar.month_future') }}
            @else
                {{ __('habits.calendar.month_summary_elapsed', ['completed' => $summary->completedDays, 'total' => $summary->totalDays]) }}
                · {{ $summary->percentage }}%
                @if ($summary->averageMood !== null)
                    · {{ __('habits.dashboard.average_mood') }}: {{ $summary->averageMood }}
                @endif
            @endif
        </x-slot>

        <div class="mx-auto grid max-w-3xl grid-cols-7 gap-1.5 sm:gap-2">
            @foreach ($this->weekdayLabels() as $label)
                <div class="pb-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</div>
            @endforeach

            @foreach ($this->weeks() as $week)
                @foreach ($week as $cell)
                    <x-habits.day-tile
                        :progress="$cell['progress']"
                        :outside="$cell['outside']"
                        :href="$this->dayUrl($cell['progress'])"
                        wire:key="day-{{ $cell['progress']->date->toDateString() }}"
                    />
                @endforeach
            @endforeach
        </div>

        <div class="mx-auto mt-6 max-w-3xl">
            <x-habits.progress-legend />
        </div>
    </x-filament::section>
</x-filament-panels::page>
