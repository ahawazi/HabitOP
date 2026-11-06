<div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-600 dark:text-gray-300" aria-label="{{ __('habits.year.legend') }}">
    @foreach ([
        0 => 'bg-gray-100 border-gray-200 dark:bg-white/5 dark:border-white/10',
        1 => 'bg-amber-200 border-amber-300 dark:bg-amber-500/30 dark:border-amber-500/40',
        2 => 'bg-sky-300 border-sky-400 dark:bg-sky-500/40 dark:border-sky-400/50',
        3 => 'bg-emerald-500 border-emerald-600 dark:bg-emerald-500/70 dark:border-emerald-400/60',
    ] as $level => $classes)
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block size-3.5 rounded-[3px] border {{ $classes }}"></span>
            {{ __("habits.levels.{$level}") }}
        </span>
    @endforeach
</div>
