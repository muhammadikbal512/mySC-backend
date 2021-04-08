<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use App\User;
use Socialite;

class isActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::user();
        if($user){
            if(!$user->is_active == 1){
                return response()->json([
                    'message'=>'Please contact your administrator to get your account active'
                ],401);
            }
            else{
                return $next($request);        
            }
        }
        else{
            $getInfo    = Socialite::driver($request->route('provider'))->stateless()->userFromToken($request->access_token);
            $user       = User::whereEmail($getInfo->email)->first();

            if(!$user->is_active == 1){
                return response()->json([
                    'message'=>'Please contact your administrator to get your account active'
                ],401);
            }
            else{
                return $next($request);        
            }
            
        }    
        return $next($request);
    }
}
