<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Performance extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    protected $casts = [
      'period' => 'array'
    ];
    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class);
    }

}
