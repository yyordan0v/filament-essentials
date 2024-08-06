<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AttendeesStatsWidget extends BaseWidget
{
    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Attendees Count', Attendee::count())
                ->descriptionIcon('heroicon-o-user-group')
                ->description('Total number of attendees')
                ->color('success')
                ->chart([1, 5, 6, 1, 10]),
            Stat::make('Total Revenue', Number::currency(Attendee::sum('ticket_cost') / 100)),
        ];
    }
}
