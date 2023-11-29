<?php

namespace App\Http\Controllers;

use Cocur\Slugify\Slugify;
use Illuminate\Support\Facades\DB;

class HelperController extends Controller
{
    public function generateUniqueSlug($id, $name, $table) // https://medium.com/@lordNeic/generate-unique-slugs-on-the-fly-for-create-and-update-in-laravel-models-87ac7e52aa1e
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($name);

        $existing_slugs = DB::table($table)->where('slug', 'LIKE', $slug . '%')
            ->where('id', '!=', $id ?? null) // Exclude the current model's ID
            ->pluck('slug')
            ->toArray();

        if (!in_array($slug, $existing_slugs)) { // Slug is unique, no need to append numbers
            return $slug;
        }

        // Increment the number until a unique slug is found
        $i = 1;
        $uniqueSlugFound = false;

        while (!$uniqueSlugFound) {
            $newSlug = $slug . '-' . $i;

            if (!in_array($newSlug, $existing_slugs)) { // Unique slug found
                return $newSlug;
            }

            $i++;
        }

        return $slug . '-' . mt_rand(1000, 9999); // Fallback: return the original slug with a random number appended
    }
}
