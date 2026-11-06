<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Register as BaseRegister;

/**
 * Registration page: the language the visitor was browsing in becomes their saved
 * preference. Default habits are created by the CreateDefaultHabits listener.
 */
class Register extends BaseRegister
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['locale'] = app()->getLocale();

        return $data;
    }
}
