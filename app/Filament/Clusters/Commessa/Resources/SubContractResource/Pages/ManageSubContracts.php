<?php

namespace App\Filament\Clusters\Commessa\Resources\SubContractResource\Pages;

use App\Filament\Clusters\Commessa\Resources\SubContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubContracts extends ManageRecords
{
    protected static string $resource = SubContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
