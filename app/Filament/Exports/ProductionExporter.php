<?php

namespace App\Filament\Exports;

use App\Models\Production;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;


class ProductionExporter extends Exporter
{

    protected static ?string $model = Production::class;


    public static function getColumns(): array
    {

//        $cachedColumns= self::getCachedColumns();
//        $date_start= $cachedColumns['date_start'];
//        $date_end= $cachedColumns['date_end'];

        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('project_id')
            ->label('Commessa'),
            ExportColumn::make('desc'),
            ExportColumn::make('type'),
            ExportColumn::make('percentage'),
            ExportColumn::make('value'),
            ExportColumn::make('imponibile'),
            ExportColumn::make('date_start'),
            ExportColumn::make('date_end'),

            ExportColumn::make('months')
                ->label('Periodo in mesi')
                ->state(function (Production $record): string {

                    $date_start = !is_null($record->date_start ?? null) ? Carbon::parse($record->date_start) : null;

                    $date_end = !is_null($record->date_end ?? null) ? Carbon::parse($record->date_end) : null;
                    $period = CarbonPeriod::create($date_start, '1 month', $date_end);

//                $p = array();
//
//                foreach ($period as $date){
//                    $p[]=(object)[
//                        "start"=> ($date->firstOfMonth()->gt($date_start)) ? $date->firstOfMonth()->toDateString(): $date_start,
//                        "end"=> ($date->lastOfMonth()->lt($date_end)) ? $date->lastOfMonth()->toDateString() : $date_end
//                    ];
//                }


                    $diffInMonths = $date_start ? $date_start->diffInMonths($date_end) : 1;
                    $subd = ((int)$diffInMonths > 1) ? $record->imponibile / (int)$diffInMonths : $record->imponibile;

                    return $diffInMonths;
                }),

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

