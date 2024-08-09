<?php

namespace App\Filament\Clusters\Commessa\Resources\ProductionResource\Pages;

use App\Filament\Clusters\Commessa\Resources\ProductionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduction extends CreateRecord
{
    protected static string $resource = ProductionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
