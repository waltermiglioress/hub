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
//            Stat::make('Produzione fatturata', '€'.$this->getPageTableQuery()->where('imponibile','>',0,'and',)->sum('imponibile'))
            Stat::make('Produzione fatturata', '€'.$this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','fatturato')->sum('imponibile'))
//                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Produzione stimata', '€'.$this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','stimato')->sum('imponibile'))
//                ->description('7% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('estimated'),
            Stat::make('Produzione contabilizzata e non fatturata', '€'.$this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','contabilizzato e non ft')->sum('imponibile'))
//                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
        ];
    }
}
