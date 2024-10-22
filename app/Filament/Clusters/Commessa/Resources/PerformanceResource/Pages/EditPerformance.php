<?php

namespace App\Filament\Clusters\Commessa\Resources\PerformanceResource\Pages;

use App\Filament\Clusters\Commessa\Resources\PerformanceResource;
use App\Traits\HandleAttachments;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerformance extends EditRecord
{
    use HandleAttachments;

    protected static string $resource = PerformanceResource::class;

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
