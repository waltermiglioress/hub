<?php

namespace App\Filament\Resources\SubContractResource\Pages;

use App\Filament\Resources\SubContractResource;
use App\Models\ComplianceDocumentSubContract;
use App\Traits\HandleAttachments;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubContract extends CreateRecord
{
    use HandleAttachments;
    protected static string $resource = SubContractResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $subContract = $this->record;

        // Ottieni lo stato del form (tutti i dati)
        $formState = $this->form->getRawState();

        // Cicla attraverso i dati del repeater per ciascun 'compliance_document'
        foreach ($formState['compliance_documents'] as $complianceDocumentData) {
            // Trova o crea il record di ComplianceDocumentSubContract
            $complianceDocument = $subContract->complianceDocumentSubContracts()->create($complianceDocumentData);

            // Verifica che ci siano allegati nel campo 'attachments'
            $attachments = $complianceDocumentData['attachments'] ?? [];

            if (!empty($attachments)) {
                // Chiama il metodo per gestire gli allegati
                $complianceDocument->handleAttachments($complianceDocument, $attachments);
            }
        }
    }
}
