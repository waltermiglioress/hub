<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $start = Carbon::parse($this->date_start);
        $end = Carbon::parse($this->date_end);

        // Crea un array per mantenere tutti i mesi toccati dall'intervallo
        $currentMonth = $start->copy()->startOfMonth();
        $endMonth = $end->copy()->startOfMonth();
        $months = [];

        while ($currentMonth->lessThanOrEqualTo($endMonth)) {
            $months[] = $currentMonth->copy();
            $currentMonth->addMonth();
        }

        // Crea un array di righe per ogni mese toccato
        $rows = [];
        foreach ($months as $month) {
            $imponibileMensile = count($months) > 0 ? $this->imponibile / count($months) : 0;

            $rows[] = [
                'desc' => $this->desc,
                'type' => $this->type,
                'percentage' => $this->percentage / 100,
                'value' => $this->value,
                'imponibile' => $this->imponibile,
                'date_start' => $start->format('d/m/Y'),
                'date_end' => $end->format('d/m/Y'),
                'status' => $this->status,
                'client_name' => $this->client->name,
                'project_code' => $this->project->code_ind,
                'month' => $month->format('d/m/Y'),
                'imponibile_mensile' => $imponibileMensile
            ];
        }

        // Restituisci l'array finale
        return $rows;
//        return [
//            'desc'=>$this->desc,
//            'type'=>$this->type,
//            'percentage'=>$this->percentage,
//            'value'=>$this->value,
//            'imponibile'=>$this->imponibile,
//            'status'=>$this->status,
//            'codCommessa'=>$this->project->code,
//            'codInd'=>$this->project->code_ind,
//        ];
    }
}
