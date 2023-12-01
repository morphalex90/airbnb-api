<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Host extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'airbnb_host_id',
        'airbnb_host_name',
        'airbnb_host_since',
        'airbnb_host_description',
    ];

    /**
     * The rooms that belong to the host.
     */
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'host_room');
    }

    /**
     * The images that belong to the room.
     */
    public function image(): HasOne
    {
        return $this->hasOne(File::class, 'entity_id')->where('entity_type', 'host');
    }
}
