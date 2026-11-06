@php
    $locales = \App\Support\Locale::supported();
    $current = app()->getLocale();
    $floating = $floating ?? false;
@endphp

<div @class([
    'flex items-center gap-1 rounded-lg bg-gray-100 p-1 text-xs font-medium dark:bg-white/5',
    'fixed top-4 end-4 z-50 shadow-sm' => $floating,
    'me-2' => ! $floating,
]) data-test="locale-switcher">
    @foreach ($locales as $code => $name)
        <a
            href="{{ route('locale.switch', $code) }}"
            hreflang="{{ $code }}"
            lang="{{ $code }}"
            @class([
                'rounded-md px-2 py-1 transition',
                'bg-white text-gray-950 shadow-sm dark:bg-white/10 dark:text-white' => $code === $current,
                'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => $code !== $current,
            ])
            @if ($code === $current) aria-current="true" @endif
        >{{ $name }}</a>
    @endforeach
</div>
