<?php

namespace App\Observers;

use App\Http\Controllers\HelperController;
use App\Models\Room;
use Cocur\Slugify\Slugify;
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
        $helper = new HelperController;

        $room->slug = $helper->generateUniqueSlug($room->id, $room->name, 'rooms');
        $room->key = Str::uuid(36);
        $room->name = Str::trim($room->name);
    }
}
