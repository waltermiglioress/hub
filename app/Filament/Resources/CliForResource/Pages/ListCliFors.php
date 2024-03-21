<?php

namespace App\Filament\Resources\CliForResource\Pages;

use App\Filament\Resources\CliForResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCliFors extends ListRecords
{
    protected static string $resource = CliForResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
