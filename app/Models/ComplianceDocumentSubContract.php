<?php

namespace App\Models;

use App\Traits\HandleAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ComplianceDocumentSubContract extends Model
{
    use HasFactory;
    use HandleAttachments;

    protected $with = ['attachments'];

    protected $table = 'compliance_document_sub_contract';  // Nome della tabella pivot

    protected $fillable = [
        'sub_contract_id',
        'compliance_document_id',
        'status',
        'notes',
        'verified_at',
    ];

    public function complianceDocument()
    {
        return $this->belongsTo(ComplianceDocument::class, 'compliance_document_id');
    }

    public function subContract()
    {
        return $this->belongsTo(SubContract::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

}
