<?php

namespace App\Http\Controllers;

use App\Experience;
use App\User;
use App\Media;
// use App\Models\LoginHistory;
// use App\Models\Location as Position;


use Illuminate\Http\Request;
use Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
// use Browser;
// use Location;

class AuthController extends Controller
{
    // This script use just only to my own check
    public function redirect($provider){
        return Socialite::driver($provider)->stateless()->redirect();
        
    }
    
    
    //Retrieve Access Token Google SSO
    public function callback(Request $request,$provider){

        $getInfo    = Socialite::driver($provider)->stateless()->userFromToken($request->access_token);

        // This script use just only to my own check
        // $getInfo    = Socialite::driver($provider)->stateless()->user();
        // dd($getInfo);


        $checkuser  = $this->checkUser($getInfo,$provider);

        $user       = User::FindOrFail($checkuser->id);

        //Store a user to experience table
        $this->experience($user);

        //Store a user to login history table
        // $this->loginHistory($user);

        $token      = Auth::login($user);

        return response()->json($this->respondWithToken($token,$user),200);

    }

    public function logout(Request $request){

        //Blacklist token from User login
        JWTAuth::invalidate(JWTAuth::parseToken());
        return response()->json([
            'message'   => 'Logout Successfully'
        ],200);
        
    }

    //Method for checking the User is it available?
    function checkUser($getInfo,$provider){

        $user           = User::whereEmail($getInfo->email)->first();

        if(!$user){
            $media = Media::create([
                'path'          => $getInfo->avatar,
            ]);
            $user = $media->user()->create([
                'name'          => $getInfo->name,
                'email'         => $getInfo->email,
                'password'      => $getInfo->password,
                'provider_id'   => $getInfo->id,
            ]);            

        }
        else{
            $media = Media::wherePath($getInfo->avatar)->first(); 
            if(!$media){
                $media = Media::create([
                    'path'          => $getInfo->avatar,
                ]);
            }
            $user->update([
                'provider_id'   => $getInfo->id,
                'media_id'      => $media->id,
            ]);
        }
        return $user;
    }

    //Generate Token From JWT
    protected function respondWithToken($token,$user){
        $data = [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 60,
            'role'          => $user->role->name,
            'name'          => $user->name,
        ];

        return $data;
    }

    //Method to Store data to Experience Model
    function experience($user){
        $exp = Experience::whereUserId($user->id)->first();
        if(!$exp){
            Experience::create([
                'user_id'   => $user->id,
                ]);
        }
        return $exp;
    }

    //Show Detail User by User Active
    public function show(){
        $token  = JWTAuth::user();
        $user   = User::FindOrFail($token->id);
        

        return response()->json([
            'User'  => [
                'Detail_user'   => $user,
                'Reviewer'      => $user->difficulties()->select('name')->get(),
                'Role'          => $user->role()->select('name')->get(),
                'Media'         => $user->media()->select('path')->get(),

            ],
        ],200);
    }

    //Method to store last login
    // function loginHistory($user){
    //     $ip             = request()->ip();
    //     $position       = Location::get($ip);
        

    //     $browser        = Browser::browserName();
    //     $os             = Browser::PlatformName();
        
    //     if(Browser::isDesktop()){
    //         $platform = 'desktop';
    //     }
    //     elseif(Browser::isTablet()){
    //         $platform = 'tablet/ipad';
    //     }
    //     elseif(Browser::isMobile()){
    //         $platform = 'mobile';
    //     }
    //     else{
    //         $platform = 'bot';
    //     }
        
        
    //     $location       = Position::create([
    //         'country_name'  => $position->countryName,
    //         'region_name'   => $position->regionName,
    //         'city_name'     => $position->cityName,
    //         'zip_code'      => $position->zipCode,
    //     ]);

    //     $login = $location->login()->create([
    //         'user_id'       => $user->id,
    //         'browser'       => $browser,
    //         'platform'      => $platform,
    //         'os'            => $os,
    //         'ip'            => $ip,
    //     ]);
        
    //     return $login;
    // }

}
