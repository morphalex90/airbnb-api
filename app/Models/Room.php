<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'room_type_id',
        'property_type_id',
        'latitude',
        'longitude',
        'postcode',
        'city_id',
        'guests',
        'bedrooms',
        'beds',
        'bathrooms',
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
        'room_type_id',
        'city_id',
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
    public function roomType(): HasOne
    {
        return $this->hasOne(RoomType::class, 'id', 'room_type_id');
    }

    /**
     * The property type that belong to the property.
     */
    public function propertyType(): HasOne
    {
        return $this->hasOne(PropertyType::class, 'id', 'property_type_id');
    }

    /**
     * The city that belong to the room.
     */
    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    /**
     * The amenities that belong to the room.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity')->orderBy('name', 'ASC');
    }

    /**
     * The images that belong to the room.
     */
    public function images(): HasMany
    {
        return $this->hasMany(File::class, 'entity_id')->where('entity_type', 'room');
    }

    /**
     * The images that belong to the room.
     */
    public function image(): HasOne
    {
        return $this->hasOne(File::class, 'entity_id')->where('entity_type', 'room');
    }

    /**
     * The hosts that belong to the room.
     */
    public function hosts(): BelongsToMany
    {
        return $this->belongsToMany(Host::class, 'host_room')->with('image');
    }
}
