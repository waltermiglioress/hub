<?php

namespace App\Filament\Clusters\Commessa\Resources\ProductionResource\Pages;

use App\Filament\Clusters\Commessa\Resources\ProductionResource;
use App\Traits\HandleAttachments;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduction extends EditRecord
{
    use HandleAttachments;
    protected static string $resource = ProductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
