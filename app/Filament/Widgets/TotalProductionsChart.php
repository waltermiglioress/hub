<?php

namespace App\Filament\Widgets;

use App\Models\Production;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class TotalProductionsChart extends ChartWidget
{
    protected static ?string $heading = 'Numero produzioni';
    protected static string $color = 'success';
    protected static ?int $sort = 3;
    protected function getData(): array
    {
//        $data = Trend::model(Production::class)
//            ->between(
//                start: now()->startOfYear(),
//                end: now()->endOfYear(),
//            )
//            ->perMonth()
//            ->count();

        $data= $this->getProductionPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Produzioni',
                    'data' => $data['productionsPerMonth'],
                ],
            ],

//            'labels' => ['Feb'],

        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getProductionPerMonth(): array
    {
        $now = Carbon::now();
        $productionPerMonth = [];
        for ($month = 1; $month <= 24; $month++) {
            $count = Production::whereYear('date_start', $now->year)
                ->whereMonth('date_start', $month)
                ->count();
            $productionPerMonth[$now->month($month)->format('M')] = $count;
//            dd($productionPerMonth);
        }

        return [
            'productionsPerMonth' => $productionPerMonth,
            'month' => $month
        ];
    }
}
