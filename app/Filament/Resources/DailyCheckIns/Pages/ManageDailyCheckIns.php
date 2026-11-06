<?php

namespace App\Filament\Resources\DailyCheckIns\Pages;

use App\Filament\Pages\CheckIn;
use App\Filament\Resources\DailyCheckIns\DailyCheckInResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageDailyCheckIns extends ManageRecords
{
    protected static string $resource = DailyCheckInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkIn')
                ->label(__('habits.check_in.today_title'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->url(fn (): string => CheckIn::getUrl()),
        ];
    }
}
