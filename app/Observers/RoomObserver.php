<?php

namespace App\Observers;

use App\Models\Room;
use Illuminate\Support\Str;

class RoomObserver
{
    /**
     * Handle the Room "creating" event.
     *
     * @param  \App\Models\Room  $room
     * @return void
     */
    public function creating(Room $room)
    {
        $room->key = Str::uuid(36);
    }
}
