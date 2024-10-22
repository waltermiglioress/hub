<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SubContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'supplier_id',
        'project_id',
        'referent',
        'attachment',
    ];
    protected $casts = [
        'attachments' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(CliFor::class, 'client_id');
    }

    public function supplier()
    {
        return $this->belongsTo(CliFor::class, 'supplier_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

//    public function compliance_documents()
//    {
//        return $this->belongsToMany(ComplianceDocument::class, 'compliance_document_sub_contract')
//            ->withPivot('status', 'notes', 'attachment', 'verified_at')
//            ->withTimestamps();
//    }

    public function complianceDocumentSubContracts()
    {
        return $this->hasMany(ComplianceDocumentSubContract::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('userProjects', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Se l'utente è un'istanza di User
                if ($user instanceof User) {
                    $userProjects = $user->projects->pluck('id');
                    $builder->whereIn('project_id', $userProjects);
                }

                // Se l'utente è un'istanza di CliFor
                if ($user instanceof CliFor) {
                    // Filtra SubContracts per i progetti associati al CliFor
                    $builder->whereHas('project', function ($query) use ($user) {
                        $query->where('clifor_id', $user->id);  // Usa clifor_id per filtrare i progetti
                    });
                }
            }

        });
    }

    /**
     * Calcola la percentuale di completamento del subappalto.
     * La percentuale si basa sul numero di documenti di conformità verificati rispetto al totale richiesto.
     */
    public function getPercentageAttribute()
    {
        // Recupera tutti i documenti associati al subappalto
        $totalDocuments = $this->complianceDocumentSubContracts()->count();

        // Recupera i documenti verificati (approvati o caricati correttamente)
        $verifiedDocuments = $this->complianceDocumentSubContracts()
            ->where('status', 'approved')  // Assumi che 'approved' significhi verificato
            ->count();

        // Evita divisioni per zero
        if ($totalDocuments === 0) {
            return 0;
        }

        // Calcola la percentuale di completamento
        return ($verifiedDocuments / $totalDocuments) * 100;
    }


    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


}
