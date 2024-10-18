<?php

namespace App\Filament\Resources\SubContractResource\Pages;

use App\Filament\Resources\SubContractResource;
use App\Models\ComplianceDocumentSubContract;
use App\Traits\HandleAttachments;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditSubContract extends EditRecord
{
    use HandleAttachments;

    protected static string $resource = SubContractResource::class;

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


    protected function beforeSave(): void
    {
        $subContract = $this->record;

        // Accedi allo stato del form tramite getState()
        $formState = $this->form->getRawState();

        // Accedi ai record della relazione 'complianceDocumentSubContracts' dallo stato del form
        foreach ($formState['compliance_documents'] as $complianceDocumentData) {
            // Trova il record di ComplianceDocumentSubContract appena aggiornato
            $complianceDocument = $subContract->complianceDocumentSubContracts()->find($complianceDocumentData['id']);

            // Ora puoi accedere agli allegati
            $attachments = $complianceDocumentData['attachments'] ?? [];

            // Chiama il metodo per gestire gli allegati
            if ($complianceDocument) {
                $complianceDocument->handleAttachments($complianceDocument, $attachments);
            }
        }
    }


//    protected function mutateFormDataBeforeFill(array $data): array
//    {
//        // Carica la relazione 'attachments' con il record di complianceDocumentSubContracts
//        $this->record->load('complianceDocumentSubContracts.attachments');
//
//        return $data;
//    }

}
