<?php

namespace App\Filament\Auth;

use App\Support\LocalCalendar;
use App\Support\Locale;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * The user's settings: profile (name, email, password) and preferences
 * (interface language and calendar system).
 */
class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return __('habits.settings.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('habits.settings.title'))
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make(__('habits.settings.tab_profile'))
                            ->key('profile')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->schema([
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                                $this->getCurrentPasswordFormComponent(),
                            ]),
                        Tab::make(__('habits.settings.tab_preferences'))
                            ->key('preferences')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->schema([
                                Select::make('locale')
                                    ->label(__('habits.profile.locale'))
                                    ->options(Locale::supported())
                                    ->selectablePlaceholder(false)
                                    ->required(),
                                Select::make('calendar')
                                    ->label(__('habits.settings.calendar'))
                                    ->helperText(__('habits.settings.calendar_help'))
                                    ->options(LocalCalendar::options())
                                    ->selectablePlaceholder(false)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['locale'] ??= app()->getLocale();
        $data['calendar'] ??= LocalCalendar::system();

        return $data;
    }

    /**
     * Reload so the new language, text direction and calendar apply immediately.
     */
    protected function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }
}
