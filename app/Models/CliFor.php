<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CliFor extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'avatar',
        'piva',
        'CF',
        'is_client',
        'is_supplier',
        'address',
        'cap',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'tel',
        'email',
        'website',
    ];

    public function country():BelongsTo{
        return $this->belongsTo(Country::class);
    }

    public function state():BelongsTo{
        return $this->belongsTo(State::class);
    }

    public function city():BelongsTo{
        return $this->belongsTo(City::class);
    }

    public function complianceDocuments():HasMany{
        return $this->hasMany(ComplianceDocument::class);
    }
}
