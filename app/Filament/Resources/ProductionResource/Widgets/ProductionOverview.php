<?php

namespace App\Filament\Resources\ProductionResource\Widgets;

use App\Filament\Resources\ProductionResource\Pages\ListProductions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class ProductionOverview extends BaseWidget
{

    use InteractsWithPageTable;
    protected static ?string $pollingInterval = null;

    protected function getTablePage():string{
        return ListProductions::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Produzione fatturata', $this->getPageTableQuery()->where('imponibile','>',0)->sum('imponibile'))
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Produzione stimata', '21%')
                ->description('7% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('estimated'),
            Stat::make('Produzione contabilizzata e non fatturata', '3:12')
                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
        ];
    }
}
