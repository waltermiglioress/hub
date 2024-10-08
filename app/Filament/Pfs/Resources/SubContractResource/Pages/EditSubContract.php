<?php

namespace App\Filament\Pfs\Resources\SubContractResource\Pages;

use App\Filament\Pfs\Resources\SubContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubContract extends EditRecord
{
    protected static string $resource = SubContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
