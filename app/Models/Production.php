<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    use HasFactory;
    protected $fillable = [
        'desc',
        'type',
        'percentage',
        'value',
        'date_start',
        'date_end',
        'status',
        'client_id',
        'ft',
        'date_ft',
        'note',
        'project_id',

    ];

    public function client(): belongsTo {
        return $this->belongsTo(CliFor::class);
    }
    public function project(): belongsTo {
        return $this->belongsTo(Project::class);
    }
}
