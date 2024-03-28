<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'code_ind',
        'desc',
        'contract',
        'status',
        'value',
        'user_id',
        'group',
        'tender_id',
        'clifor_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

//
    public function tender():belongsTo{
        return $this->belongsTo(Tender::class);
    }
    public function clifor():belongsTo{
        return $this->belongsTo(CliFor::class);
    }
//
    public function manager():belongsTo{
        return $this->belongsTo(People::class,'responsible_id');
    }
//
    public function users():BelongsToMany{
        return $this->belongsToMany(User::class,'project_user')->withTimestamps();
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
