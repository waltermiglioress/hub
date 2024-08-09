<?php

namespace App\Filament\Clusters\Commessa\Resources\PerformanceResource\Pages;

use App\Filament\Clusters\Commessa\Resources\PerformanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePerformance extends CreateRecord
{
    protected static string $resource = PerformanceResource::class;

    protected static ?string $title = 'Nuovo Indice';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
