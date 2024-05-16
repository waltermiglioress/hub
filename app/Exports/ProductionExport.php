<?php

namespace App\Exports;

use App\Models\Production;
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
        $start = new \DateTime($row->date_start);
        $end = new \DateTime($row->date_end);
        $originalDay = $start->format('d');

        $months = 1 + (($end->format('Y') - $start->format('Y')) * 12) + ($end->format('m') - $start->format('m'));
        for ($i = 0; $i < $months; $i++) {
            //$month = $start->format('d/m/Y');
            $currentMonth = clone $start;
            $year = (int)$currentMonth->format('Y');
            $month = (int)$currentMonth->format('m') + $i;

            // Gestisce il cambio di anno e il numero del mese
            $year += intdiv($month - 1, 12);
            $month = ($month - 1) % 12 + 1;

            // Calcola il giorno corretto per evitare il problema del salto
            $day = min($originalDay, (int)(new DateTime("$year-$month-01"))->format('t'));
            $currentMonth->setDate($year, $month, $day);


            $imponibileMensile = $row->imponibile / $months;


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
                $currentMonth->format('Y-m'),
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
