<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ __('habits.week.title') }}</x-slot>
        <x-slot name="description">{{ __('habits.week.subtitle') }}</x-slot>

        <x-slot name="afterHeader">
            <div class="flex items-center gap-1">
                <x-filament::icon-button
                    :icon="\Filament\Support\Icons\Heroicon::OutlinedChevronLeft"
                    class="rtl:rotate-180"
                    wire:click="previousWeek"
                    :label="__('habits.week.previous')"
                    data-test="previous-week"
                />
                <span class="min-w-36 text-center text-sm font-medium text-gray-700 dark:text-gray-200">{{ $this->weekLabel() }}</span>
                <x-filament::icon-button
                    :icon="\Filament\Support\Icons\Heroicon::OutlinedChevronRight"
                    class="rtl:rotate-180"
                    wire:click="nextWeek"
                    :disabled="$this->isCurrentWeek()"
                    :label="__('habits.week.next')"
                    data-test="next-week"
                />
                @unless ($this->isCurrentWeek())
                    <x-filament::link tag="button" wire:click="currentWeek" size="sm">{{ __('habits.week.current') }}</x-filament::link>
                @endunless
            </div>
        </x-slot>

        @if ($this->stats->isEmpty())
            <div class="flex flex-col items-center gap-3 py-8 text-center">
                <x-filament::icon icon="heroicon-o-sparkles" class="size-10 text-gray-400" />
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('habits.empty.no_habits') }}</p>
                <x-filament::button :href="$this->habitsUrl()" tag="a" icon="heroicon-o-plus">{{ __('habits.empty.create') }}</x-filament::button>
            </div>
        @else
            <div class="-mx-6 overflow-x-auto">
                <table class="w-full min-w-[720px] border-collapse text-sm" data-test="weekly-matrix">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 dark:border-white/10 dark:text-gray-400">
                            <th class="px-6 py-3 text-start font-medium">{{ __('habits.week.habit') }}</th>
                            @foreach ($this->days as $day)
                                <th @class(['px-1 py-3 text-center font-medium', 'text-primary-600 dark:text-primary-400' => $day['isToday']])>
                                    <a href="{{ $this->checkInUrl($day['date']) }}" class="hover:underline" title="{{ __('habits.week.open_day', ['date' => $day['number']]) }}">
                                        <div>{{ $day['label'] }}</div>
                                        <div class="text-xs font-normal opacity-75">{{ $day['number'] }}</div>
                                    </a>
                                </th>
                            @endforeach
                            <th class="px-6 py-3 text-center font-medium">{{ __('habits.week.weekly_progress') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($this->stats as $stat)
                            @php($habit = $stat->habit)
                            <tr wire:key="habit-row-{{ $habit->id }}" class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl" style="background-color: {{ $habit->color }}22; color: {{ $habit->color }}">
                                            <x-filament::icon :icon="$habit->iconName()" class="size-5" />
                                        </span>
                                        <div class="min-w-0">
                                            <div class="truncate font-medium text-gray-950 dark:text-white">{{ $habit->displayName() }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $habit->category->label() }}</div>
                                        </div>
                                    </div>
                                </td>

                                @foreach ($this->days as $day)
                                    @php($done = $stat->isCompletedOn($day['date']))
                                    <td class="px-1 py-3 text-center">
                                        <button
                                            type="button"
                                            wire:click="toggle({{ $habit->id }}, '{{ $day['date'] }}')"
                                            wire:loading.attr="disabled"
                                            @disabled(! $day['isRecordable'])
                                            aria-pressed="{{ $done ? 'true' : 'false' }}"
                                            aria-label="{{ $habit->displayName() }} — {{ $day['number'] }}"
                                            data-test="toggle-{{ $habit->id }}-{{ $day['date'] }}"
                                            @class([
                                                'mx-auto flex size-9 items-center justify-center rounded-xl border-2 transition focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none',
                                                'text-white shadow-sm' => $done,
                                                'border-gray-200 text-transparent hover:border-gray-400 dark:border-white/10 dark:hover:border-white/30' => ! $done && $day['isRecordable'],
                                                'cursor-not-allowed border-dashed border-gray-200 text-transparent opacity-40 dark:border-white/10' => ! $day['isRecordable'],
                                                'ring-2 ring-primary-500/30' => $day['isToday'] && ! $done,
                                            ])
                                            @if ($done) style="background-color: {{ $habit->color }}; border-color: {{ $habit->color }}" @endif
                                        >
                                            <x-filament::icon icon="heroicon-m-check" class="size-5" />
                                        </button>
                                    </td>
                                @endforeach

                                <td class="w-48 px-6 py-3">
                                    <div class="mb-1 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                                        <span>{{ __('habits.week.target_progress', ['done' => $stat->completedCount(), 'target' => $stat->target]) }}</span>
                                        <span class="tabular-nums">{{ $stat->percentage() }}%</span>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                        <div class="h-full rounded-full transition-all duration-300" style="width: {{ $stat->percentage() }}%; background-color: {{ $habit->color }}"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
