<?php

namespace App\Filament\Auth;

use App\Support\Locale;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

/**
 * Profile page with an extra "interface language" preference.
 */
class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                Select::make('locale')
                    ->label(__('habits.profile.locale'))
                    ->options(Locale::supported())
                    ->selectablePlaceholder(false)
                    ->required(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['locale'] ??= app()->getLocale();

        return $data;
    }

    /**
     * Reload so the new language (and text direction) applies immediately.
     */
    protected function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }
}
