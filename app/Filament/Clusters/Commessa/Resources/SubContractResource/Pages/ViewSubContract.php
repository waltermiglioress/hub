<?php

namespace App\Filament\Clusters\Commessa\Resources\SubContractResource\Pages;
use App\Filament\Clusters\Commessa\Resources\SubContractResource;
use App\Models\SubContract;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
class ViewSubContract extends ViewRecord
{

    protected static string $resource = SubContractResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var SubContract */
        $record = $this->getRecord();

        return $record->project->desc;
    }
}
