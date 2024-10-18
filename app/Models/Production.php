<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'desc',
        'type',
        'percentage',
        'value',
        'imponibile',
        'date_start',
        'date_end',
        'status',
        'client_id',
        'ft',
        'date_ft',
        'note',
        'project_id',

    ];

    protected $casts = [
        "value" => "float"
    ];

    public function client(): belongsTo
    {
        return $this->belongsTo(CliFor::class);
    }

    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
