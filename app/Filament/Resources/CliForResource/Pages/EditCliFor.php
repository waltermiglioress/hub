<?php

namespace App\Filament\Resources\CliForResource\Pages;

use App\Filament\Resources\CliForResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCliFor extends EditRecord
{
    protected static string $resource = CliForResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
