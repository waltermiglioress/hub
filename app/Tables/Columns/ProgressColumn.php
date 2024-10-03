<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\Column;

class ProgressColumn extends Column
{
    protected string $view = 'tables.columns.progress-column';


    public function getBackgroundColor(int $percentage): string
    {
        if ($percentage <= 25) {
            return '#f56565';  // Rosso (Tailwind bg-red-500)
        } elseif ($percentage <= 50) {
            return '#ecc94b';  // Giallo (Tailwind bg-yellow-500)
        } elseif ($percentage <= 75) {
            return '#4299e1';  // Blu (Tailwind bg-blue-500)
        }

        return '#48bb78';  // Verde (Tailwind bg-green-500)
    }
}
