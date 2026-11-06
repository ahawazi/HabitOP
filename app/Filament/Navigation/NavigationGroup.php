<?php

namespace App\Filament\Navigation;

use Filament\Support\Contracts\HasLabel;

/**
 * The panel's sidebar groups, with translated labels.
 */
enum NavigationGroup implements HasLabel
{
    case Tracker;
    case Manage;

    public function getLabel(): string
    {
        return match ($this) {
            self::Tracker => __('habits.nav.group_tracker'),
            self::Manage => __('habits.nav.group_manage'),
        };
    }
}
