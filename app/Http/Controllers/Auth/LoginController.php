<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Laravel\Socialite\Facades\Socialite;
use Socialite;

class LoginController extends Controller
{
    //redirect user to the google authentication page
    public function redirectToProvider() 
    {
        return \Socialite::driver('google')->redirect();
    }

    //obtain the user information from GitHub
    public function handleProviderCallback()
    {
        $user = \Socialite::driver('google')->user();
    }

    public function retrieveUser($user)
    {
        $user = \Socialite::driver('google')->userFromToken($user);

        return response()->json($user, 200);
    }
}
