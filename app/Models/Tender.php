<?php

namespace App\Models;

use App\Enums\TenderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tender extends Model
{
    use HasFactory;

    protected $fillable =[
        'rdo',
        'cig',
        'num',
        'type',
        'date_in',
        'desc',
        'inspection',
        'date_end',
        'mode',
        'status',
        'country_id',
        'state_id',
        'city_id',
        'group',
        'clifor_id',
    ];

    protected $casts = [
        'type' => 'boolean',
        'inspection' => 'boolean',
        'status' => TenderStatusEnum::class
    ];

    public function clifor(): BelongsTo{
        return $this->belongsTo(CliFor::class,'clifor_id');
    }

    public function country():BelongsTo{
        return $this->belongsTo(Country::class);
    }

    public function state():BelongsTo{
        return $this->belongsTo(State::class);
    }

    public function city():BelongsTo{
        return $this->belongsTo(City::class);
    }

    public function specs(): HasOne{
        return $this->HasOne(TenderSpecs::class);
    }
}
