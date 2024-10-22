<?php

namespace App\Filament\Resources\SubContractResource\Pages;

use App\Filament\Resources\SubContractResource;
use App\Models\ComplianceDocumentSubContract;
use App\Traits\HandleAttachments;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSubContract extends CreateRecord
{
    use HandleAttachments;

    protected static string $resource = SubContractResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }





    protected function afterCreate(): void
    {
        $subContract = $this->record;

        // Recupera lo stato del form, incluso il repeater compliance_documents
        $formState = $this->form->getRawState();

        // Cicla attraverso i documenti di conformità nel repeater
        foreach ($formState['compliance_documents'] as $index => $complianceDocumentData) {
            // Verifica se il compliance_document_id è presente
            if (empty($complianceDocumentData['compliance_document_id'])) {
                continue;  // Salta i record incompleti
            }

            // Trova il record che è stato creato con sub_contract_id ma ha campi null
            $existingRecord = $subContract->complianceDocumentSubContracts()
                ->whereNull('compliance_document_id')  // Cerca i record con campi null
                ->first();

            if ($existingRecord) {
                // Aggiorna il record trovato con i dati corretti
                $existingRecord->update([
                    'compliance_document_id' => $complianceDocumentData['compliance_document_id'],
                    'notes' => $complianceDocumentData['notes'] ?? null,
                    'status' => $complianceDocumentData['status'] ?? 'pending',
                    'verified_at' => $complianceDocumentData['verified_at'] ?? null,
                ]);

                // Gestisci eventuali allegati (se presenti)
                $attachments = $complianceDocumentData['attachments'] ?? [];
                foreach ($attachments as $path) {
                    $existingRecord->attachments()->create([
                        'filename' => basename($path),
                        'path' => $path,
                    ]);
                }
            } else {
                // Se non esiste nessun record da aggiornare, crea un nuovo record
                $newRecord = $subContract->complianceDocumentSubContracts()->create([
                    'compliance_document_id' => $complianceDocumentData['compliance_document_id'],
                    'notes' => $complianceDocumentData['notes'] ?? null,
                    'status' => $complianceDocumentData['status'] ?? 'pending',
                    'verified_at' => $complianceDocumentData['verified_at'] ?? null,
                ]);

                // Gestisci eventuali allegati
                $attachments = $complianceDocumentData['attachments'] ?? [];
                foreach ($attachments as $path) {
                    $newRecord->attachments()->create([
                        'filename' => basename($path),
                        'path' => $path,
                    ]);
                }
            }
        }
    }
}
