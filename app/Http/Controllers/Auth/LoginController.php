<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Laravel\Socialite\Facades\Socialite;
use Socialite;

class LoginController extends Controller
{
    public function retrieveUser($token)
    {
        $user = Socialite::driver('google')->userFromToken($token);
        
        return response()->json($user, 200);
    }
}
