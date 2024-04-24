<?php

namespace App\Filament\Widgets;

use App\Models\Production;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DoughnutProductions extends ChartWidget
{
    protected static ?string $heading = 'Produzioni';
    protected static ?int $sort =2;
    protected static ?string $maxHeight = '350px';

    protected function getData(): array
    {

        $data = $this->getTypeOfProduction();

        return [
            'datasets' => [
                [
                    'label' => 'Produzioni',
                    'data' => $data['produzioni'],
                    'backgroundColor' => [
                        'rgb(5, 150, 105)',
                        'rgb(255, 99,132)',
                        'rgb(234, 88,12)',
                        'rgb(54, 162,235)',
                        'rgb(3,7,18)',
                    ],
                ],
            ],

            'labels' => [
                'Fatturato',
                'Stimato',
                'Contabilizzato',
                'In Corso',
            ],

        ];


    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    private function getTypeOfProduction(): array
    {


        return [
            'produzioni' => [
                Production::where('imponibile', '>', 0, 'and')->where('status', 'fatturato')->count(),
                Production::where('imponibile', '>', 0, 'and')->where('status', 'stimato')->count(),
                Production::where('imponibile', '>', 0, 'and')->where('status', 'contabilizzato e non ft')->count(),
                Production::where('imponibile', '>', 0, 'and')->where('status', 'in corso')->count()
            ]
        ];
    }

    protected function getOptions(): array|\Filament\Support\RawJs|null
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false]
            ],
        ];
    }
}
