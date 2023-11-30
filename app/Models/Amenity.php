<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
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
     *  Room uri
     */
    public function getUriAttribute()
    {
        return '/amenity/' . $this->slug;
    }
}
