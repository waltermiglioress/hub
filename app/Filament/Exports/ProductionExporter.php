<?php

namespace App\Filament\Exports;

use App\Models\Production;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductionExporter extends Exporter
{
    protected static ?string $model = Production::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('desc'),
            ExportColumn::make('type'),
            ExportColumn::make('percentage'),
            ExportColumn::make('value'),
            ExportColumn::make('imponibile'),
            ExportColumn::make('date_start'),
            ExportColumn::make('date_end'),
            ExportColumn::make('status'),
            ExportColumn::make('ft'),
            ExportColumn::make('date_ft'),
            ExportColumn::make('note'),
            ExportColumn::make('client.name'),
//            ExportColumn::make('project.id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your production export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
