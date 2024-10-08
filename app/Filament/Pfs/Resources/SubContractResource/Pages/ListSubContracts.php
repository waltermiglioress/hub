<?php

namespace App\Filament\Pfs\Resources\SubContractResource\Pages;

use App\Filament\Pfs\Resources\SubContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubContracts extends ListRecords
{
    protected static string $resource = SubContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
