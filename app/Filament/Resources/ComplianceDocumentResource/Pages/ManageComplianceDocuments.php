<?php

namespace App\Filament\Resources\ComplianceDocumentResource\Pages;

use App\Filament\Resources\ComplianceDocumentResource;
use App\Traits\HandleAttachments;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageComplianceDocuments extends ManageRecords
{
    use HandleAttachments;
    protected static string $resource = ComplianceDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
