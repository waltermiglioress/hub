<?php

namespace App\Filament\Resources\SubContractResource\Pages;

use App\Filament\Resources\SubContractResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubContract extends CreateRecord
{
    protected static string $resource = SubContractResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
