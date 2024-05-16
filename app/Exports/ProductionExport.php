<?php

namespace App\Exports;

use App\Models\Production;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        $interval = $start->diff($end);
        // Calcola i mesi basandoti sugli anni e mesi
        $months = $interval->m + ($interval->y * 12);

        // Se ci sono giorni extra, considera se aggiungere un mese
        if ($interval->d > 0) {
            $months++;
        }

        // Mi assicuro di avere almeno un mese
        $months = max(1, $months);
//        dd($interval);
//        $months = max(1, $interval->m + ($interval->y * 12));

        for ($i = 0; $i < $months; $i++) {
            $month = $start->format('d/m/Y');
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
                $month,
                $row->imponibile / $months
            ];
            $start->modify('+1 month');
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
