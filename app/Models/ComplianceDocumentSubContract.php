<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceDocumentSubContract extends Model
{
    use HasFactory;

    protected $table = 'compliance_document_sub_contract';  // Nome della tabella pivot

    protected $fillable = [
        'sub_contract_id',
        'compliance_document_id',
        'status',
        'notes',
        'attachment',
        'verified_at',
    ];

    public function complianceDocument()
    {
        return $this->belongsTo(ComplianceDocument::class,'compliance_document_id');
    }

    public function subContract()
    {
        return $this->belongsTo(SubContract::class);
    }
}
