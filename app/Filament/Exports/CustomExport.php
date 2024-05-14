<?php

namespace App\Filament\Exports;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class CustomExport extends ExcelExport{

    protected ?Component $livewire = null;
    protected ?string $modelKeyName;

    protected array $recordIds = [];
    protected ?array $formData;

    public function hydrate($livewire = null, $records = null, $formData = null): static
    {
        $this->livewire = $livewire;
        $this->modelKeyName = $this->getModelInstance()->getQualifiedKeyName();
        $this->recordIds = $records?->pluck($this->getModelInstance()->getKeyName())->toArray() ?? [];

        $this->formData = ['$formData','bo'];

        return dd($this);
    }

//    public function getRecordIds(): array
//    {
//        dd($this->formData);
//    }


}
