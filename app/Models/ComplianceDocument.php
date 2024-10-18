<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ComplianceDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'desc',
        'template',
        'cli_fors_id',
    ];
    protected $casts = [
        'template' => 'array',
    ];

    public function subContracts()
    {
        return $this->belongsToMany(SubContract::class, 'compliance_document_sub_contract')
            ->withPivot('status', 'notes', 'attachment', 'verified_at')
            ->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(CliFor::class, 'cli_fors_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
