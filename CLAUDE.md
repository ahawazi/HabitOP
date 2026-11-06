# CLAUDE.md

Guidance for AI coding agents working in this repository.

## What this is

HabitFlow: a personal habit tracker on Laravel 13 + Filament 5 (Livewire 4, Tailwind 4, Pest 4, Larastan level 7). One public landing page (`resources/views/welcome.blade.php`, route `home`); everything else is the Filament panel `dashboard` at `/dashboard` (`app/Providers/Filament/DashboardPanelProvider.php`), including login, registration, password reset and profile.

## Commands

```bash
composer setup                       # first install
composer dev                         # serve + queue + vite
php artisan test --compact           # Pest (RefreshDatabase applied to tests/Feature)
vendor/bin/pint --dirty              # format changed PHP files (run after every PHP edit)
composer types:check                 # phpstan level 7
npm run build                        # landing CSS + Filament theme (resources/css/filament/dashboard/theme.css)
```

## Architecture rules

- **Every user is the super admin of their own data, and only of their own.** `User::canAccessPanel()` is always true; isolation comes from ownership:
  - Models carry `user_id`, which is never mass-assignable; it is set with `->user()->associate($user)` (see `SaveDailyCheckIn`, `ToggleHabitCompletion`, `ManageHabits` create action).
  - Every read goes through `ownedBy($user)` scopes; Filament resources override `getEloquentQuery()` to scope to the signed-in user.
  - `HabitPolicy` / `DailyCheckInPolicy` check ownership. Keep `tests/Feature/Panel/IsolationTest.php` passing.
- **Domain logic lives in actions**, not in Filament pages: `app/Actions/Habits/*`. Pages/widgets only call actions and render DTOs from `app/Data/*`.
- **Progress rules**: only *active* habits count. A day is complete when every active habit is done; streaks are built from complete days (today being incomplete does not break the current streak). Levels (0–3) are percentage based so any number of habits works. Weekly targets come from `habits.target_per_week`.
- **Check-in rows are lazy**: no row means 0%. Both the check-in form and the weekly matrix write the same `daily_check_ins` / `habit_completions` rows.
- **Dates**: `TrackerPeriod::forUser()` defines what is navigable (1 Jan of sign-up year → 31 Dec of current year) and recordable (… → today). Weeks start per locale via `App\Support\Week` (fa: Saturday, en: Monday).
- **i18n**: every UI string is in `lang/{en,fa}/habits.php` — add keys to both files. Locale resolution: user preference → session → `app.locale` (`App\Support\Locale`, `SetLocale` middleware on the web group and the panel). Never hard-code date formats; use `habits.formats.*`.
- **Panel middleware must not be persistent** (`isPersistent: true` re-runs `StartSession` on Livewire requests and logs users out).
- Custom Blade in panel views needs its Tailwind classes compiled by the Filament theme (`@source` paths in `resources/css/filament/dashboard/theme.css`). Shared Blade components live in `resources/views/components/habits/`.

## Conventions

- PHP 8.3, typed signatures, PHPDoc over inline comments, `#[Fillable]`/`#[Hidden]` attributes on models.
- Tests: Pest feature tests, factories (`User::factory()` creates the default habits; use `->withoutHabits()` to skip), helpers `completeDay()` / `partialDay()` in `tests/Pest.php`.
