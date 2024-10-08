<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class CliFor extends Authenticatable implements FilamentUser
{
    use HasFactory,HasRoles,Notifiable,HasPanelShield;

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


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'=>'hashed',
        'is_client'=>'boolean',
        'is_supplier'=>'boolean'
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

    public function canAccessPanel(Panel $panel): bool
    {
       return true;
    }
}
