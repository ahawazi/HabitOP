@props([
    'progress',
    'href' => null,
    'variant' => 'calendar',
    'outside' => false,
])

@php
    /** @var \App\Data\DailyProgress $progress */
    $level = $progress->level();

    // Each level differs in lightness as well as hue, so the states remain
    // distinguishable without colour perception. The accessible label below always
    // states the percentage outright.
    $fill = match (true) {
        $progress->isFuture => 'bg-white dark:bg-white/5 border-dashed border-gray-300 dark:border-gray-700',
        $level === 0 => 'bg-gray-100 border-gray-200 dark:bg-white/5 dark:border-white/10',
        $level === 1 => 'bg-amber-200 border-amber-300 dark:bg-amber-500/30 dark:border-amber-500/40',
        $level === 2 => 'bg-sky-300 border-sky-400 dark:bg-sky-500/40 dark:border-sky-400/50',
        default => 'bg-emerald-500 border-emerald-600 dark:bg-emerald-500/70 dark:border-emerald-400/60',
    };

    // The day in the other calendar system (Gregorian when Jalali is active and vice versa).
    $alternate = \App\Support\LocalCalendar::alternate();
    $equivalentDay = \App\Support\LocalCalendar::format($progress->date, __('habits.formats.day'), $alternate);
    $equivalentMonth = \App\Support\LocalCalendar::format($progress->date, __('habits.formats.month'), $alternate);
    $isEquivalentMonthStart = \App\Support\LocalCalendar::format($progress->date, 'j', $alternate) === \App\Support\LocalCalendar::digits(1);

    $date = __('habits.calendar.with_equivalent', [
        'date' => \App\Support\LocalCalendar::format($progress->date, __('habits.formats.long_date')),
        'equivalent' => \App\Support\LocalCalendar::format($progress->date, __('habits.formats.day_month_year'), $alternate),
    ]);

    $label = $progress->isFuture
        ? __('habits.day_summary.future', ['date' => $date])
        : ($progress->moodScore !== null
            ? __('habits.day_summary.with_mood', [
                'date' => $date,
                'completed' => $progress->completedCount,
                'total' => $progress->totalCount,
                'percentage' => $progress->percentage,
                'mood' => $progress->moodScore,
                'max' => \App\Models\DailyCheckIn::MOOD_MAX,
            ])
            : __('habits.day_summary.without_mood', [
                'date' => $date,
                'completed' => $progress->completedCount,
                'total' => $progress->totalCount,
                'percentage' => $progress->percentage,
            ]));

    if ($progress->isToday) {
        $label = __('habits.calendar.today_badge').' — '.$label;
    }

    $tag = $href ? 'a' : 'span';
@endphp

@if ($variant === 'heatmap')
    <{{ $tag }}
        @if ($href) href="{{ $href }}" wire:navigate @endif
        title="{{ $label }}"
        aria-label="{{ $label }}"
        data-test="year-day"
        data-date="{{ $progress->date->toDateString() }}"
        data-level="{{ $progress->isFuture ? 'future' : $level }}"
        @class([
            'block size-3.5 rounded-[3px] border transition sm:size-4',
            'hover:scale-125 hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300' => $href,
            $fill,
            'ring-2 ring-gray-900 ring-offset-1 ring-offset-white dark:ring-white dark:ring-offset-gray-900' => $progress->isToday,
        ])
    ></{{ $tag }}>
@else
    <{{ $tag }}
        @if ($href) href="{{ $href }}" wire:navigate @endif
        title="{{ $label }}"
        aria-label="{{ $label }}"
        aria-current="{{ $progress->isToday ? 'date' : 'false' }}"
        data-test="calendar-day"
        data-date="{{ $progress->date->toDateString() }}"
        data-level="{{ $progress->isFuture ? 'future' : $level }}"
        @class([
            'relative flex aspect-square flex-col items-center justify-center gap-0.5 rounded-xl border p-1 transition',
            'hover:z-10 hover:scale-105 hover:shadow-sm hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300' => $href,
            $fill,
            'opacity-40' => $outside,
            'ring-2 ring-gray-900 dark:ring-white' => $progress->isToday,
        ])
    >
        <span @class([
            'text-sm font-semibold leading-none tabular-nums',
            'text-white' => $level === 3 && ! $progress->isFuture,
            'text-gray-900 dark:text-white' => $level !== 3 || $progress->isFuture,
        ])>{{ \App\Support\LocalCalendar::format($progress->date, __('habits.formats.day')) }}</span>

        <span
            aria-hidden="true"
            data-test="calendar-day-equivalent"
            @class([
                'absolute start-1 top-1 max-w-[calc(100%-0.5rem)] truncate text-[9px] leading-none tabular-nums sm:start-1.5 sm:top-1.5 sm:text-[10px]',
                'text-white/80' => $level === 3 && ! $progress->isFuture,
                'text-gray-500 dark:text-gray-400' => $level !== 3 || $progress->isFuture,
                'font-semibold' => $isEquivalentMonthStart,
            ])
        >{{ $equivalentDay }}@if ($isEquivalentMonthStart)<span class="hidden sm:inline"> {{ $equivalentMonth }}</span>@endif</span>

        <span @class([
            'text-[10px] leading-none tabular-nums',
            'text-white/90' => $level === 3 && ! $progress->isFuture,
            'text-gray-600 dark:text-gray-300' => $level !== 3 || $progress->isFuture,
        ])>
            @if ($progress->isFuture) &mdash; @else {{ $progress->percentage }}% @endif
        </span>

        @if ($progress->moodScore !== null)
            <span class="hidden text-[10px] leading-none text-gray-600 tabular-nums sm:inline dark:text-gray-300" aria-hidden="true">&#9829;{{ $progress->moodScore }}</span>
        @endif

        @if ($level === 3 && ! $progress->isFuture)
            <x-filament::icon icon="heroicon-m-check-circle" class="absolute end-1 bottom-1 size-3.5 text-white/90" />
        @endif
    </{{ $tag }}>
@endif
