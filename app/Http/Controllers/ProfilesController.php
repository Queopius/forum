<?php

namespace App\Http\Controllers;

use App\Models\{Activity, User};

class ProfilesController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @param  User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(User $user)
    {
        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => Activity::feed($user)
        ]);
    }
}
