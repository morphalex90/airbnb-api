<?php

namespace App\Http\Controllers;

use Cocur\Slugify\Slugify;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HelperController extends Controller
{
    public function generateUniqueSlug($id, $name, $table) // https://medium.com/@lordNeic/generate-unique-slugs-on-the-fly-for-create-and-update-in-laravel-models-87ac7e52aa1e
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($name);

        $existing_slugs = DB::table($table)->where('slug', 'LIKE', $slug . '%')
            ->where('id', '!=', $id ?? null) // exclude current model ID
            ->pluck('slug')
            ->toArray();

        if (!in_array($slug, $existing_slugs)) { // slug is unique, no need to append numbers
            return $slug;
        }

        // increment the number until a unique slug is found
        $i = 1;
        $uniqueSlugFound = false;

        while (!$uniqueSlugFound) {
            $newSlug = $slug . '-' . $i;

            if (!in_array($newSlug, $existing_slugs)) { // unique slug found
                return $newSlug;
            }

            $i++;
        }

        return $slug . '-' . mt_rand(1000, 9999); // fallback: return the original slug with a random number appended
    }

    public function removeEmoji($string)
    {
        // Match Enclosed Alphanumeric Supplement
        $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
        $clear_string = preg_replace($regex_alphanumeric, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Supplemental Symbols and Pictographs
        $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
        $clear_string = preg_replace($regex_supplemental, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }

    public function getAddressFromCoordinates($latitude, $longitude)
    {
        // $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . $latitude . '&lon=' . $longitude . '&zoom=12&addressdetails=1';
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude . ',' . $longitude . '&sensor=false&key=' . getenv('GOOGLE_MAPS_API_KEY');

        $response = Http::get($url);
        if ($response->status() == 200) {
            $body = json_decode($response->body());

            $return = [];
            foreach ($body->results[0]->address_components as $component) {
                if (
                    !in_array('street_number', $component->types) &&
                    !in_array('postal_code', $component->types) &&
                    !in_array('postal_code_suffix', $component->types) &&
                    !in_array('administrative_area_level_2', $component->types) &&
                    !in_array('administrative_area_level_3', $component->types) &&
                    !in_array('point_of_interest', $component->types) &&
                    !in_array('subpremise', $component->types) &&
                    !in_array('premise', $component->types) &&
                    !in_array('route', $component->types)
                ) {

                    if (in_array('country', $component->types)) {
                        $return[] = $component->short_name;
                    } else {
                        $return[] = $component->long_name;

                        // $return[] = [
                        //     'name' => $component->long_name,
                        //     'types' => $component->types[0],
                        // ];
                    }
                }
            }
            return $return;
        }

        return false;
    }
}
