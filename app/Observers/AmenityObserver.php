<?php

namespace App\Observers;

use App\Http\Controllers\HelperController;
use App\Models\Amenity;
use Illuminate\Support\Str;

class AmenityObserver
{
    /**
     * Handle the Amenity "creating" event.
     *
     * @param  \App\Models\Amenity  $amenity
     * @return void
     */
    public function creating(Amenity $amenity)
    {
        $helper = new HelperController;

        $amenity->slug = $helper->generateUniqueSlug($amenity->id, $amenity->name, 'amenities');
        $amenity->key = Str::uuid(36);
    }
}
