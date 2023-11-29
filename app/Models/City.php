<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public $timestamps = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'name',
        'parent',
        'slug',
    ];

    /**
     * The calculated fields.
     *
     * @var array
     */
    protected $appends = [
        'uri',
    ];

    /**
     *  City uri
     */
    public function getUriAttribute()
    {
        return '/city/' . $this->slug;
    }

    /**
     * Get the rooms associated with the city.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class)->orderBy('name');
    }
}
