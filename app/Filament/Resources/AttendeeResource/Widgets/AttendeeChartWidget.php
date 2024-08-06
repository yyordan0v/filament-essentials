<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Filament\Resources\AttendeeResource\Pages\ListAttendees;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Collection;

class AttendeeChartWidget extends ChartWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Chart';

    protected static ?string $maxHeight = '200px';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = '3months';

    /**
     * @param  string|null  $filter
     * @return Collection
     */
    public function filterData(?string $filter): Collection
    {
        $query = $this->getPageTableQuery();

        match ($filter) {
            'week' => $data = Trend::query($query)
                ->between(
                    start: now()->subWeeks(1),
                    end: now(),
                )
                ->perDay()
                ->count(),
            'month' => $data = Trend::query($query)
                ->between(
                    start: now()->subMonths(1),
                    end: now(),
                )
                ->perDay()
                ->count(),
            '3months' => $data = Trend::query($query)
                ->between(
                    start: now()->subMonths(3),
                    end: now(),
                )
                ->perMonth()
                ->count(),
        };

        return $data;
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last Week',
            'month' => 'Last Month',
            '3months' => 'Last 3 Months',
        ];
    }

    protected function getTablePage(): string
    {
        return ListAttendees::class;
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        $data = $this->filterData($filter);

        return [
            'datasets' => [
                [
                    'label' => 'Signups',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
