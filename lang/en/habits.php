<?php

/*
|--------------------------------------------------------------------------
| Habit Tracker Language Lines
|--------------------------------------------------------------------------
|
| Every user-facing string of the app lives here; lang/fa/habits.php mirrors
| this file key for key. Keys are structured and language-neutral so the
| interface can be flipped to RTL without touching any Blade template.
|
*/

return [

    'brand' => 'HabitFlow',

    'formats' => [
        'long_date' => 'l, j F Y',
        'day_month' => 'j M',
        'day_month_year' => 'j M Y',
        'month' => 'F',
        'month_year' => 'F Y',
        'weekday_short' => 'D',
        'day' => 'j',
    ],

    'names' => [
        'exercise' => 'Exercise',
        'reading' => 'Reading',
        'journaling' => 'Journaling',
    ],

    'categories' => [
        'health' => 'Health',
        'fitness' => 'Fitness',
        'mind' => 'Mind & focus',
        'work' => 'Work & skills',
        'learning' => 'Learning',
        'other' => 'Other',
    ],

    'nav' => [
        'dashboard' => 'Dashboard',
        'check_in' => 'Daily check-in',
        'calendar' => 'Calendar',
        'year_overview' => 'Year overview',
        'habits' => 'My habits',
        'history' => 'Check-in history',
        'group_tracker' => 'Tracker',
        'group_manage' => 'Manage',
        'landing' => 'Back to website',
        'settings' => 'Settings',
    ],

    'landing' => [
        'meta_description' => 'HabitFlow is a personal habit tracker: daily check-ins, weekly goals, streaks, mood and a full-year heatmap.',
        'login' => 'Log in',
        'register' => 'Get started — free',
        'open_dashboard' => 'Open my dashboard',
        'badge' => 'Your own private habit space',
        'title' => 'Build habits that actually stick',
        'subtitle' => 'Tick off your habits each day, set weekly goals, log your mood and watch your streaks grow — in a dashboard that belongs only to you.',
        'preview_title' => 'This week',
        'features_title' => 'Everything you need, nothing you don\'t',
        'features' => [
            'check_in' => ['title' => 'Daily check-ins', 'body' => 'Mark what you completed, rate your mood from 1 to 10 and leave a short note for any day.'],
            'weekly' => ['title' => 'Weekly goals', 'body' => 'Give every habit a weekly target and tick days off in a one-tap weekly matrix.'],
            'streaks' => ['title' => 'Streaks & stats', 'body' => 'Current and best streaks, weekly and monthly completion and your average mood.'],
            'calendar' => ['title' => 'Calendar & year heatmap', 'body' => 'See every month at a glance and a whole year of progress on one screen.'],
            'custom' => ['title' => 'Your habits, your way', 'body' => 'Create, colour, categorise, reorder and archive as many habits as you like.'],
            'private' => ['title' => 'Private by design', 'body' => 'Every account is its own admin space. Nobody else can ever see your data.'],
        ],
        'steps_title' => 'Up and running in a minute',
        'steps' => [
            '1' => ['title' => 'Create your account', 'body' => 'Sign up with your email — three starter habits are ready for you.'],
            '2' => ['title' => 'Make it yours', 'body' => 'Rename, add or archive habits and set a weekly goal for each.'],
            '3' => ['title' => 'Check in daily', 'body' => 'A few taps a day. The dashboard does the rest.'],
        ],
        'cta_title' => 'Start your first streak today',
        'cta_body' => 'Free, private and ready in under a minute.',
        'footer' => 'Built with Laravel & Filament.',
    ],

    'dashboard' => [
        'greeting' => 'Hi, :name',
        'intro' => 'Here is how your habits are going.',
        'this_week' => 'This week',
        'this_month' => 'This month',
        'weekly_goals' => 'Weekly goals',
        'weekly_goals_hint' => 'Progress towards each habit\'s weekly target',
        'current_streak' => 'Current streak',
        'best_streak' => 'Best streak',
        'average_mood' => 'Average mood',
        'average_mood_hint' => 'Days with a recorded mood this month',
        'completed_days_elapsed' => ':completed of :total elapsed days complete',
        'view_calendar' => 'Open calendar',
        'view_year' => 'Open year overview',
        'manage_habits' => 'Manage habits',
    ],

    'charts' => [
        'weekly_title' => 'Weekly progress',
        'weekly_subtitle' => 'Habits completed per day this week',
        'completed_habits' => 'Completed habits',
        'trend_title' => 'Last 30 days',
        'trend_subtitle' => 'Daily completion (%) and mood',
        'completion' => 'Completion %',
        'mood' => 'Mood',
    ],

    'check_in' => [
        'title' => 'Daily check-in',
        'today_title' => 'Today\'s check-in',
        'habits_label' => 'Habits',
        'mood_label' => 'Mood',
        'mood_description' => 'Optional. How did you feel today, from 1 to 10?',
        'mood_clear' => 'Clear',
        'mood_score_of' => ':mood / :max',
        'mood_value' => 'Mood :mood of :max',
        'notes_label' => 'Notes',
        'notes_placeholder' => 'Anything worth remembering about today?',
        'save' => 'Save check-in',
        'saved' => 'Check-in saved.',
        'previous_day' => 'Previous day',
        'next_day' => 'Next day',
        'go_to_today' => 'Today',
        'future_notice' => 'This date is in the future. You can look ahead, but habits and mood can only be recorded once the day arrives.',
        'progress' => 'Daily progress',
        'progress_hint' => 'A day counts towards your streak only when every active habit is completed.',
        'habits_completed' => ':completed of :total habits',
        'all_done' => 'All done!',
        'shortcuts' => 'Shortcuts',
    ],

    'week' => [
        'title' => 'Weekly habits',
        'subtitle' => 'Tap a box to tick a habit off for that day.',
        'habit' => 'Habit',
        'weekly_progress' => 'Weekly goal',
        'target_progress' => ':done of :target',
        'open_day' => 'Open the check-in for :date',
        'previous' => 'Previous week',
        'next' => 'Next week',
        'current' => 'This week',
    ],

    'calendar' => [
        'title' => 'Monthly calendar',
        'subtitle' => 'Every day at a glance. Select a date to open its check-in.',
        'previous_month' => 'Previous month',
        'next_month' => 'Next month',
        'this_month' => 'This month',
        'month_summary_elapsed' => ':completed of :total elapsed days complete',
        'month_future' => 'This month hasn\'t started yet — check back once it arrives.',
        'today_badge' => 'Today',
        'with_equivalent' => ':date (:equivalent)',
    ],

    'year' => [
        'title' => 'Year overview',
        'subtitle' => 'Your whole :year at a glance.',
        'overall_completion' => 'Overall completion',
        'completed_days' => 'Completed days',
        'current_streak' => 'Current streak',
        'best_streak' => 'Best streak',
        'legend' => 'Progress legend',
    ],

    'levels' => [
        '0' => 'Nothing done',
        '1' => 'Under half',
        '2' => 'Half or more',
        '3' => 'Everything done',
    ],

    'day_summary' => [
        'with_mood' => ':date — :completed of :total habits (:percentage%), mood :mood of :max',
        'without_mood' => ':date — :completed of :total habits (:percentage%)',
        'future' => ':date — upcoming',
    ],

    'streak' => [
        'days' => '{0} 0 days|{1} :count day|[2,*] :count days',
        'rule' => 'A day counts towards a streak only when every active habit is completed.',
        'rule_short' => 'Days with every habit done',
    ],

    'resource' => [
        'habit' => 'habit',
        'habits' => 'habits',
        'subtitle' => 'Add, edit, reorder (drag) or archive your habits.',
        'restore_defaults' => 'Add starter habits',
        'defaults_restored' => 'Starter habits added.',
        'fields' => [
            'name' => 'Name',
            'name_placeholder' => 'e.g. Read 30 minutes',
            'category' => 'Category',
            'color' => 'Colour',
            'icon' => 'Icon',
            'target_per_week' => 'Weekly target (days)',
            'target_help' => 'How many days a week you aim to do this habit.',
            'is_active' => 'Active',
            'is_active_help' => 'Archived habits are hidden from check-ins and statistics.',
            'this_week' => 'This week',
        ],
    ],

    'history' => [
        'title' => 'check-ins',
        'check_in' => 'check-in',
        'date' => 'Date',
        'completed' => 'Completed',
        'completed_value' => ':completed of :total',
        'mood' => 'Mood',
        'notes' => 'Notes',
        'from' => 'From',
        'until' => 'Until',
        'with_notes' => 'With notes only',
        'open' => 'Open',
    ],

    'profile' => [
        'locale' => 'Interface language',
    ],

    'settings' => [
        'title' => 'Settings',
        'tab_profile' => 'Profile',
        'tab_preferences' => 'Language & dates',
        'calendar' => 'Calendar',
        'calendar_help' => 'Months, years and every date in the tracker follow this calendar. The calendar page also shows the equivalent date in the other one.',
        'calendars' => [
            'jalali' => 'Solar Hijri (Jalali)',
            'gregorian' => 'Gregorian',
        ],
    ],

    'months' => [
        'jalali' => [
            1 => 'Farvardin',
            2 => 'Ordibehesht',
            3 => 'Khordad',
            4 => 'Tir',
            5 => 'Mordad',
            6 => 'Shahrivar',
            7 => 'Mehr',
            8 => 'Aban',
            9 => 'Azar',
            10 => 'Dey',
            11 => 'Bahman',
            12 => 'Esfand',
        ],
    ],

    'validation' => [
        'future_date' => 'You cannot record a check-in for a future date.',
        'mood_range' => 'The mood score must be a whole number between :min and :max.',
        'invalid_date' => 'That date is not part of the tracked period.',
    ],

    'empty' => [
        'no_habits' => 'You have no active habits yet.',
        'create' => 'Create a habit',
    ],

];
