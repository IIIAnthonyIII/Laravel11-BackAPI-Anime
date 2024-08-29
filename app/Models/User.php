<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'status',
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Get the attributes that should be cast.
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // JWT Functions
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    //Tables relations
    public function anime()
    {
        return $this->hasMany(Anime::class);
    }

    public function type()
    {
        return $this->hasMany(Type::class);
    }             
}
