<?php

namespace App\Filament\AvatarProviders;

use App\Models\User;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * Inline SVG avatars with the user's initials, so the panel never depends on an
 * external avatar service.
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initials = $record instanceof User ? $record->initials() : '?';

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><rect width="64" height="64" fill="#10b981"/><text x="50%%" y="50%%" dy=".35em" text-anchor="middle" font-family="Vazirmatn,Arial,sans-serif" font-size="26" font-weight="600" fill="#fff">%s</text></svg>',
            e(mb_strtoupper($initials)),
        );

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
