<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'user_id',
        'key',
        'airbnb_id',
        'airbnb_host_id',
        'name',
        'description',
        'type_id',
        'latitude',
        'longitude',
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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'airbnb_id',
        'airbnb_host_id',
        'type_id',
        'deleted_at',
    ];
    /**
     *  Room uri
     */
    public function getUriAttribute()
    {
        return '/room/' . $this->slug;
    }

    /**
     * The room type that belong to the room.
     */
    public function type(): HasOne
    {
        return $this->hasOne(RoomType::class, 'id', 'type_id');
    }

    /**
     * The amenities that belong to the room.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity');
    }
}
