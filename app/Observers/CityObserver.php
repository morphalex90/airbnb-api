<?php

namespace App\Observers;

use App\Http\Controllers\HelperController;
use App\Models\City;
use Illuminate\Support\Str;

class CityObserver
{
    /**
     * Handle the City "creating" event.
     *
     * @param  \App\Models\City  $city
     * @return void
     */
    public function creating(City $city)
    {
        $helper = new HelperController;

        $city->slug = $helper->generateUniqueSlug($city->id, $city->name, 'cities');
        $city->key = Str::uuid(36);
    }
}
