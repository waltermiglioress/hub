<?php

namespace App\Exports;

use App\Models\Production;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, ShouldAutoSize
{

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
//        dd($query);
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Descrizione Attività',
            'Tipo',
            'Percentuale',
            'Valore lavorazione',
            'Imponibile',
            'Data Inizio',
            'Data Fine',
            'Stato',
            'Client',
            'Codice Industriale',
            'Mese di competenza',
            'Imponibile Mensile'
        ];
    }

    public function map($row): array
    {
        $rows = [];
        $start = Carbon::parse($row->date_start);
        $end = Carbon::parse($row->date_end);

        // Crea un array per mantenere tutti i mesi toccati dall'intervallo
        $currentMonth = $start->copy()->startOfMonth();
        $endMonth = $end->copy()->startOfMonth();
        $months = [];

        while ($currentMonth->lessThanOrEqualTo($endMonth)) {
            $months[] = $currentMonth->copy(); // Aggiungi il mese corrente all'array
            $currentMonth->addMonth(); // Vai al prossimo mese

        }
        Log::info('Dopo il while months: '.$currentMonth);
        foreach ($months as $month) {

            $imponibileMensile = $row->imponibile / count($months);


            $rows[] = [
                $row->desc,
                $row->type,
                $row->percentage / 100,
                $row->value,
                $row->imponibile,
                $start->format('d/m/Y'),
                $end->format('d/m/Y'),
//                $row->date_start->format('d/m/Y'),
//                $row->date_end->format('d/m/Y'),
                $row->status,
                $row->client->name,
                $row->project->code_ind,
                $month->format('d/m/Y'),
                $imponibileMensile
            ];

        }

        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_PERCENTAGE_0,
            'D' => NumberFormat::FORMAT_CURRENCY_EUR,
            'E' => NumberFormat::FORMAT_CURRENCY_EUR,
            'L' => NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

        ];
    }
}
