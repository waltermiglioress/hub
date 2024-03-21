<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderAttrBuEnum: string implements HasLabel
{

    case PIPELINE = 'pipeline';
    case PLANT = 'impianti';
    case ENVIRONMENTAL = 'ambientale';
    case MAINTENANCE = 'manutenzione';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
