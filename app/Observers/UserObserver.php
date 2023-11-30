<?php

namespace App\Observers;

use App\Http\Controllers\HelperController;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $helper = new HelperController;

        $user->slug = $helper->generateUniqueSlug($user->id, $user->username, 'users');
        $user->key = Str::uuid(36);
        $user->email = Str::lower($user->email);
        $user->status = 1;
    }
}
