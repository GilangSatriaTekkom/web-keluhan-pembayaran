<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'location',
        'phone',
        'about',
        'role',
        'status',
        'tgl_daftar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tagihans(): HasMany {
        return $this->hasMany(Tagihan::class);
    }

    public function historiPembayarans(): HasMany {
        return $this->hasMany(HistoriPembayaran::class);
    }

    public function langganans(): HasMany {
        return $this->hasMany(Langganan::class);
    }

    public function tikets(): HasMany {
        return $this->hasMany(Tiket::class);
    }


    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->isDirty('paket_internet_id')) {
                PaketInternetChanged::dispatch(
                    $user,
                    $user->getOriginal('paket_internet_id')
                );
            }
        });
    }

}
