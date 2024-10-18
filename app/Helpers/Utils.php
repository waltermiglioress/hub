<?php

namespace App\Helpers;

use App\Models\SubContract;
use App\Models\User;
use App\Models\CliFor;

class Utils
{
    /**
     * Verifica se l'utente (User o CliFor) ha il permesso.
     */
    public static function checkPermission($user, string $permission): bool
    {
        // Verifica che l'utente sia un'istanza di User o CliFor
        if ($user instanceof User || $user instanceof CliFor) {
            return $user->can($permission);
        }

        // Se non è né User né CliFor, ritorna false
        return false;
    }

    /**
     * Ottieni il numero di documenti di conformità che non sono ancora stati approvati.
     *
     * @param SubContract|int $subContract
     * @return int
     */
    public static function getSubContractNumbersToLoad($subContract): int
    {
        // Se viene passato un ID, recupera l'istanza di SubContract
        if (is_int($subContract)) {
            $subContract = SubContract::find($subContract);
        }

        // Verifica che l'istanza esista
        if (!$subContract) {
            return 0;
        }

        // Conta i documenti di conformità non approvati (stato diverso da 'approved')
        return $subContract->complianceDocumentSubContracts()
            ->where('status', '!=', 'approved')
            ->count();
    }

//    public static function getApprovalStatus($record): string
//    {
//        // Recupera tutti i subcontratti associati (elementi del repeater) dal form
//        $items = $record->complianceDocumentSubContracts ?? [];
//
//        // Conta il numero di documenti approvati
//        $approvedCount = collect($items)
//            ->filter(fn($item) => isset($item['status']) && $item['status'] === 'approved')
//            ->count();
//
//        // Conta il totale dei documenti (gli elementi del repeater)
//        $totalCount = count($items);
//
//        // Restituisce una stringa del tipo "3/5 Documenti approvati"
//        return "Documentazione del subappaltatore - {$approvedCount}/{$totalCount}";
//    }

    public static function calculateApprovalCounts($record): array
    {
        // Recupera tutti i subcontratti associati (elementi del repeater) dal record
        $items = $record->complianceDocumentSubContracts ?? [];

        // Conta il numero di documenti approvati
        $approvedCount = collect($items)
            ->filter(fn($item) => isset($item['status']) && $item['status'] === 'approved')
            ->count();

        // Conta il totale dei documenti (gli elementi del repeater)
        $totalCount = count($items);

        // Restituisce i conteggi come array
        return [$approvedCount, $totalCount];
    }

    public static function getApprovalStatus($record): string
    {
        [$approvedCount, $totalCount] = self::calculateApprovalCounts($record);

        // Restituisce una stringa del tipo "Documentazione del subappaltatore - 3/5"
        return "Documentazione del subappaltatore - {$approvedCount}/{$totalCount}";
    }
}
