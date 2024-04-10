<?php

namespace App\Filament\Resources\ProductionResource\Widgets;

use App\Filament\Resources\ProductionResource\Pages\ListProductions;
use App\Models\Production;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;


class ProductionOverview extends BaseWidget
{

    use InteractsWithPageTable;
    protected static ?string $pollingInterval = null;


    protected function getTablePage():string{
        return ListProductions::class;
    }
    protected function getStats(): array
    {


        $fatturato= $this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','fatturato')->sum('imponibile');
        $stimato= $this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','stimato')->sum('imponibile');
        $contabilizzatonft= $this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','contabilizzato e non ft')->sum('imponibile');
        $incorso= $this->getPageTableQuery()->where('imponibile','>',0,'and')->where('status','in corso')->sum('imponibile');

        $formatNumber = function (int $number): string {
            if ($number < 1000) {
                return (string) Number::format($number, 0);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 2) . 'k';
            }

            return Number::format($number / 1000000, 2) . 'Mln';
        };

        return [

            Stat::make('Produzione fatturata', '€'.$formatNumber($fatturato))
//                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->extraAttributes([
                    'class' => 'mng-wdg-prd mng-inv-card']),
            Stat::make('Produzione stimata', '€'.$formatNumber($stimato))
//                ->description('7% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-down')

                ->color('estimated')
                ->extraAttributes([
                    'class' => 'mng-wdg-prd mng-est-card']),
            Stat::make('Produzione contabilizzata e non fatturata','€'.$formatNumber($contabilizzatonft))
//                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'mng-wdg-prd mng-cont-card']),
            Stat::make('Produzione in corso', '€'.$formatNumber($incorso))
//                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'mng-wdg-prd mng-wip-card']),
        ];
    }
}
