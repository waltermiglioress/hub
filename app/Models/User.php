<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'tel',
        'email',
        'avatar',
        'CF',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'cap',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@sicilsaldo.it');
    }

    public function getFilamentName(): string
    {
        return "$this->name $this->username";
    }

    public function country():BelongsTo{
        return $this->belongsTo(Country::class);
    }

    public function state():BelongsTo{
        return $this->belongsTo(State::class);
    }

    public function city():BelongsTo{
        return $this->belongsTo(City::class);
    }

    public function projects():BelongsToMany{
        return $this->belongsToMany(Project::class,'project_user')->withTimestamps();
    }
}
