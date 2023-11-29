<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Cities Index
     * @group Cities
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $cities = City::orderBy('name', 'ASC')->whereNull('parent')->paginate(50, ['*'], 'p', request()->get('p', 1));

        if ($cities) {
            return response()->json(['cities' => $cities], 200);
        }
        return response()->json(['message' => 'No cities available'], 404);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * City Show
     * @group Cities
     * @authenticated
     *
     * @urlParam city_slug numeric required Example: best-city-ever
     */
    public function show(Request $request): JsonResponse
    {
        $sub_cities = [];
        $city_ids = [];

        $city = City::where('slug', $request->route('city_slug'))->first();
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $city_ids[] = $city->id;
        if ($city->parent == null) { // it's a parent city! let's load all child cities!
            $sub_cities = City::withCount('rooms as rooms')->where('parent', $city->id)->orderBy('name', 'ASC')->get();
            $city_ids = $sub_cities->pluck('id')->toArray();
        }

        $rooms = Room::with('city') // load rooms
            ->whereHas('city', function ($q) use ($city_ids) {
                $q->whereIn('city_id', $city_ids);
            })
            ->orderBy('name', 'ASC')
            ->paginate(30, ['*'], 'p');


        return response()->json(['city' => $city, 'sub_cities' => $sub_cities, 'rooms' => $rooms], 200);
    }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
