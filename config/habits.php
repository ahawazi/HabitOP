<?php

use Carbon\CarbonInterface;

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | The interface languages a visitor or user can switch between. The key is
    | the locale code, the value is the language's own name (shown in switchers).
    |
    */

    'locales' => [
        'fa' => 'فارسی',
        'en' => 'English',
    ],

    /*
    |--------------------------------------------------------------------------
    | First Day Of The Week (per locale)
    |--------------------------------------------------------------------------
    |
    | Weekly statistics, the weekly matrix and the calendar grid all start on
    | this day. Iranian weeks start on Saturday; most English locales on Monday.
    |
    */

    'week_starts_on' => [
        'fa' => CarbonInterface::SATURDAY,
        'en' => CarbonInterface::MONDAY,
        'default' => CarbonInterface::MONDAY,
    ],

    /*
    |--------------------------------------------------------------------------
    | Calendar System (per locale)
    |--------------------------------------------------------------------------
    |
    | "jalali" or "gregorian". Decides where months and years start in the
    | calendar, the year overview and the tracked period, and how dates read.
    |
    */

    'calendars' => [
        'fa' => 'jalali',
        'en' => 'gregorian',
        'default' => 'gregorian',
    ],

    /*
    |--------------------------------------------------------------------------
    | Backfill Window
    |--------------------------------------------------------------------------
    |
    | How far back a user may record check-ins, counted in whole years before
    | the year they registered in. 0 means "from 1 January of the sign-up year".
    |
    */

    'backfill_years' => (int) env('HABITS_BACKFILL_YEARS', 0),

];
