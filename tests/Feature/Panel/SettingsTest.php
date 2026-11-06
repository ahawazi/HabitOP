<?php

use App\Data\TrackerPeriod;
use App\Filament\Auth\EditProfile;
use App\Filament\Pages\Calendar;
use App\Filament\Pages\CheckIn;
use App\Models\User;
use App\Support\LocalCalendar;
use App\Support\Locale;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

beforeEach(function () {
    // Saturday 16 Aban 1405.
    $this->travelTo(CarbonImmutable::parse('2026-11-07 09:00:00'));
});

test('the settings page is linked from the sidebar and renders both tabs', function () {
    $this->actingAs(User::factory()->create(['locale' => 'en']));

    $this->get(EditProfile::getUrl())
        ->assertOk()
        ->assertSee('Settings')
        ->assertSee('Profile')
        ->assertSee('Language &amp; dates', false)
        ->assertSee('Solar Hijri (Jalali)');
});

test('the calendar preference defaults to the language\'s calendar', function (string $locale, string $calendar) {
    Locale::apply($locale);
    $this->actingAs(User::factory()->create(['locale' => $locale]));

    Livewire::test(EditProfile::class)->assertSchemaStateSet(['calendar' => $calendar]);
})->with([
    'persian' => ['fa', 'jalali'],
    'english' => ['en', 'gregorian'],
]);

test('profile details and the calendar can be changed from settings', function () {
    $user = User::factory()->create(['locale' => 'en']);
    $this->actingAs($user);

    Livewire::test(EditProfile::class)
        ->fillForm(['name' => 'Sara', 'locale' => 'fa', 'calendar' => 'gregorian'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh())
        ->name->toBe('Sara')
        ->locale->toBe('fa')
        ->calendar->toBe('gregorian');
});

test('unknown calendars are rejected', function () {
    $user = User::factory()->create(['locale' => 'en']);
    $this->actingAs($user);

    Livewire::test(EditProfile::class)
        ->fillForm(['calendar' => 'lunar'])
        ->call('save')
        ->assertHasFormErrors(['calendar']);

    expect($user->fresh()->calendar)->toBeNull();
});

test('a gregorian preference wins over the persian default', function () {
    Locale::apply('fa');
    $user = User::factory()->create(['locale' => 'fa', 'calendar' => 'gregorian', 'created_at' => '2026-02-01']);
    $this->actingAs($user);

    expect(LocalCalendar::isJalali())->toBeFalse()
        ->and(LocalCalendar::monthKey(CarbonImmutable::parse('2026-11-07')))->toBe('2026-11')
        ->and(TrackerPeriod::forUser($user)->start->toDateString())->toBe('2026-01-01');

    Livewire::test(Calendar::class)->assertSet('month', '2026-11');
});

test('a jalali preference works in english with english month names', function () {
    Locale::apply('en');
    $user = User::factory()->create(['locale' => 'en', 'calendar' => 'jalali', 'created_at' => '2026-02-01']);
    $this->actingAs($user);

    expect(LocalCalendar::format(CarbonImmutable::parse('2026-11-07'), __('habits.formats.long_date')))->toBe('Saturday, 16 Aban 1405');

    Livewire::test(Calendar::class)
        ->assertSet('month', '1405-08')
        ->assertSee('Aban 1405')
        ->assertSee('October – November 2026');
});

test('the calendar shows the equivalent date in the other calendar', function () {
    Locale::apply('en');
    $user = User::factory()->create(['locale' => 'en', 'created_at' => '2026-02-01']);
    $this->actingAs($user);

    $calendar = Livewire::test(Calendar::class)
        ->assertSee('November 2026')
        ->assertSee('Aban – Azar 1405');

    // 7 November 2026 is 16 Aban 1405.
    $calendar->assertSeeHtml('Saturday, 7 November 2026 (16 Aban 1405)');

    expect(Livewire::test(CheckIn::class)->instance()->getSubheading())->toBe('Saturday, 7 November 2026 (16 Aban 1405)');
});
