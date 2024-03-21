<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    use HasFactory;
    protected $fillable = [
        'doc_id',
        'desc',
        'type',
        'percentage',
        'value',
        'date_start',
        'date_end',
        'status',
        'ft',
        'date_ft',
        'note'
    ];

    public function client(): belongsTo {
        return $this->belongsTo(CliFor::class,'client');
    }
    public function projects(): belongsTo {
        return $this->belongsTo(Project::class);
    }
}
