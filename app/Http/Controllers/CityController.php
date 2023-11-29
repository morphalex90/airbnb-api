<?php

namespace App\Http\Controllers;

use App\Models\City;
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
        $city = City::where('slug', $request->route('city_slug'))->first();
        if ($city) {
            if ($city->parent == null) { // it's a parent city! let's load all child!
                $sub_cities = City::where('parent', $city->id)->orderBy('name', 'ASC')->paginate(50, ['*'], 'p');
            } else {
                $city->setRelation('rooms', $city->rooms()->paginate(50, ['*'], 'p'));
            }
            return response()->json(['city' => $city, 'sub_cities' => $sub_cities], 200);
        }
        return response()->json(['message' => 'City not found'], 404);
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
