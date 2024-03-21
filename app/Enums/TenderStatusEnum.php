<?php


namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderStatusEnum: string implements HasLabel

{
    case I = 'inviata';
    case NP = 'non partecipato';
    case A = 'aggiudicata';
    case NA = 'non aggiudicata';
    case AN = 'annullata';

    public function getLabel(): ?string
    {
        return $this->name;

        // or

        return match ($this) {
            self::I => 'Inviata',
            self::NP => 'Non partecipato',
            self::A => 'Aggiudicata',
            self::NA => 'Non aggiudicata',
            self::AN => 'Annullata',
        };
    }
}
