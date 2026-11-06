# HabitFlow

[فارسی](#فارسی) | English

A personal habit tracker built with **Laravel 13** and **Filament 5**. It merges the two earlier projects:

- **HabitTracker** — the domain logic: daily check-ins with mood and notes, progress/streak calculations, calendar and year views, actions/DTOs/policies.
- **HabitFlow** — custom habits with category, colour and a weekly target, the one-tap weekly matrix and the weekly chart.

There is one public page (the landing page at `/`). Login, registration and everything after it live in a single Filament panel at `/dashboard`. **Every user is the super admin of their own space**: they can manage everything in the panel, but every query is scoped to the rows they own, so nobody can ever see another user's data.

## Features

- **Landing page** (fa/en, RTL/LTR) with login / register calls to action.
- **Filament auth**: login, registration, password reset and profile (incl. interface language).
- **Dashboard**: weekly/monthly completion, weekly-goal rate, current/best streak and average mood; the weekly habit × day matrix (tap to tick); weekly bar chart; 30-day completion + mood trend.
- **Daily check-in**: tick habits, rate your mood (1–10) and add a note for any day from 1 January of your sign-up year until today.
- **Calendar** (month grid) and **Year overview** (heatmap), every day links to its check-in.
- **My habits**: create, edit, colour, categorise, set a weekly target, choose an icon, drag to reorder, archive or delete.
- **Check-in history**: filterable list of past check-ins.
- **Bilingual**: Persian (default, RTL, Saturday week start) and English (LTR, Monday week start). Visitors switch from the header; signed-in users get the choice saved to their profile.

## Requirements

- PHP 8.3+ with `intl`, `pdo_sqlite` (or another database), `mbstring`, `zip`
- Composer 2, Node.js 20+ and npm

## Getting started

```bash
composer setup        # composer install, .env, key, sqlite db, migrate, npm install + build
php artisan db:seed   # optional: demo user test@example.com / password with ~2 months of history
composer dev          # php artisan serve + queue + vite
```

Open `http://localhost:8000`.

## Configuration

| Key | Env | Default | Description |
|---|---|---|---|
| `app.locale` | `APP_LOCALE` | `fa` | Default interface language |
| `app.timezone` | `APP_TIMEZONE` | `Asia/Tehran` | Decides what "today" is |
| `habits.backfill_years` | `HABITS_BACKFILL_YEARS` | `0` | Extra years before the sign-up year that can be backfilled |
| `habits.week_starts_on` | — | fa: Saturday, en: Monday | First day of the week per locale |

## Project structure

- `app/Models/{Habit,DailyCheckIn,HabitCompletion}.php` — user-owned domain models (`ownedBy()` scopes).
- `app/Actions/Habits/` — `EnsureDefaultHabits`, `SaveDailyCheckIn`, `ToggleHabitCompletion`, `GetHabitProgress`.
- `app/Data/` — `DailyProgress`, `ProgressSummary`, `StreakSummary`, `TrackerPeriod`, `WeeklyHabitStat`.
- `app/Filament/` — panel pages (`Dashboard`, `CheckIn`, `Calendar`, `YearOverview`), resources (`Habits`, `DailyCheckIns`), widgets, auth pages.
- `app/Providers/Filament/DashboardPanelProvider.php` — the panel.
- `app/Support/{Locale,Week}.php`, `app/Http/Middleware/SetLocale.php` — language and week handling.
- `lang/{en,fa}/habits.php` — every UI string.

## Testing & code quality

```bash
php artisan test --compact     # Pest
vendor/bin/pint                # code style
composer types:check           # Larastan level 7
```

---

## فارسی

یک اپ شخصی ردیابی عادت با **لاراول ۱۳** و **فیلامنت ۵** که دو پروژه‌ی قبلی را تلفیق می‌کند:

- **HabitTracker** — منطق اصلی: چک‌این روزانه با حال و یادداشت، محاسبه‌ی درصد و استریک، تقویم و نمای سالانه، اکشن‌ها/DTOها/پالیسی‌ها.
- **HabitFlow** — عادت‌های دلخواه با دسته‌بندی، رنگ و هدف هفتگی، جدول هفتگی تیک‌زدنی و نمودار هفتگی.

فقط یک صفحه‌ی عمومی داریم (لندینگ در `/`). ورود، ثبت‌نام و همه‌چیز بعد از آن داخل یک پنل فیلامنت در `/dashboard` است. **هر کاربر سوپر ادمین فضای خودش است**: همه‌ی امکانات پنل را دارد، اما همه‌ی کوئری‌ها به رکوردهای خود او محدود شده‌اند و هیچ‌کس داده‌ی دیگری را نمی‌بیند.

### راه‌اندازی

```bash
composer setup        # نصب وابستگی‌ها، .env، کلید، دیتابیس sqlite، مایگریشن، بیلد فرانت
php artisan db:seed   # اختیاری: کاربر نمونه test@example.com با رمز password
composer dev
```

سپس `http://localhost:8000` را باز کنید.

### نکات

- زبان پیش‌فرض فارسی (راست‌چین، شروع هفته از شنبه) است و از هدر سایت یا پنل می‌شود به انگلیسی رفت؛ انتخاب کاربرِ واردشده در پروفایلش ذخیره می‌شود.
- تاریخ‌ها فعلاً میلادی‌اند (با نام ماه‌های فارسی).
- با ثبت‌نام، سه عادت پیش‌فرض (ورزش، مطالعه، نوشتن روزانه) ساخته می‌شود.
