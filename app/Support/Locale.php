<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * The supported interface languages and how the active one is chosen.
 */
class Locale
{
    /**
     * The session key holding a guest's chosen locale.
     */
    public const string SESSION_KEY = 'locale';

    /**
     * Get the supported locales as code => native name.
     *
     * @return array<string, string>
     */
    public static function supported(): array
    {
        return (array) config('habits.locales');
    }

    /**
     * Determine whether the given locale is supported.
     */
    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::supported());
    }

    /**
     * Resolve the locale for the given request.
     */
    public static function resolve(Request $request): string
    {
        $user = $request->user();

        $candidates = [
            $user instanceof User ? $user->locale : null,
            $request->hasSession() ? $request->session()->get(self::SESSION_KEY) : null,
            (string) config('app.locale'),
        ];

        foreach ($candidates as $candidate) {
            if (self::isSupported($candidate)) {
                return $candidate;
            }
        }

        return (string) config('app.fallback_locale', 'en');
    }

    /**
     * Activate the given locale for the application and date formatting.
     */
    public static function apply(string $locale): void
    {
        App::setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
    }

    /**
     * Determine whether the active locale is written right-to-left.
     */
    public static function isRtl(?string $locale = null): bool
    {
        return in_array($locale ?? App::getLocale(), ['fa', 'ar', 'he', 'ur'], true);
    }

    /**
     * Get the text direction of the active locale.
     */
    public static function direction(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }
}
