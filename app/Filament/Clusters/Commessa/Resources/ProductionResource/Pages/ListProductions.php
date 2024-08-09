<?php

namespace App\Filament\Clusters\Commessa\Resources\ProductionResource\Pages;

use App\Filament\Clusters\Commessa\Resources\ProductionResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListProductions extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = ProductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }



    protected function getHeaderWidgets(): array
    {
        return [
            ProductionResource\Widgets\ProductionOverview::class
        ];
    }


}
