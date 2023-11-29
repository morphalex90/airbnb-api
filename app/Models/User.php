<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'key',
        'first_name',
        'last_name',
        'username',
        'role_id',
        'email',
        'password',
        'registration_ip_address',
        'country_id',
        'slug',
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

    /**
     *  User uri
     */
    public function getUriAttribute()
    {
        return '/user/' . $this->slug;
    }

    /**
     * The country that belong to the user.
     */
    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id')->select('id', 'name');
    }

    /**
     * The role that belong to the user.
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id')->select('id', 'name');
    }

    /**
     * Get the rooms associated with the user.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class, 'user_id');
    }
}
