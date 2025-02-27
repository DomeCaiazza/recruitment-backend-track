<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{

    public function taxProfile()
    {
        return $this->hasMany(TaxProfile::class);
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::created(function ($resource) {
            Log::info('User created', ['id' => $resource->id, 'email' => $resource->email]);
        });

        static::updated(function ($resource) {
            Log::info('User updated',
            ['id' => $resource->id,
            'email' => $resource->email,
            'name' => $resource->name,
            'surname' => $resource->surname]);
        });

        static::deleted(function ($resource) {
            Log::info('User deleted', ['id' => $resource->id]);
        });
    }
}
