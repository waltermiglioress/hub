<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

enum TenderAttrFluidEnum: string implements HasLabel
{

    case WATER = 'acqua';
    case OIL = 'olio';
    case METANO = 'metano';
    case IDROGENO = 'idrogeno';
    case PETROLIO = 'petrolio';

    public function getLabel(): ?string
    {
        return $this->name;

        // or

        return match ($this) {
            self::WATER => 'Acqua',
            self::OIL => 'Olio',
            self::METANO => 'Metano',
            self::IDROGENO => 'Idrogeno',
            self::PETROLIO => 'Petrolio',

        };
    }
}
