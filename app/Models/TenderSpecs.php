<?php

namespace App\Models;

use App\Enums\TenderAttrBuEnum;
use App\Enums\TenderAttrFluidEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderSpecs extends Model
{
    use HasFactory;

    protected $fillable = [
        'bu',
        'fluid_type',
        'lenght',
        'inches',
        'n_hdd',
        'diameter_hdd',
        'n_microt',
        'diameter_microt',
        'n_bvs'
    ];

    protected $casts = [
        'bu'=> TenderAttrBuEnum::class,
        'fluid_type' => TenderAttrFluidEnum::class
    ];

    public function tender(): BelongsTo{
        return $this->belongsTo(Tender::class);
    }
}
