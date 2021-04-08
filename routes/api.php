<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/auth/callback/{provider}','AuthController@callback');

Route::post('/auth/logout','AuthController@logout');
//Detail User
Route::post('/auth/user','AuthController@show')->middleware('jwt.auth');




// Route::get('login/google', 'Auth\LoginController@redirectToProvider');
// Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');
// Route::get('login/user/{user}', 'Auth\LoginController@retrieveUser');


Route::resource('users', 'UserController');



Route::group(['prefix'=>'user','middleware'=>['jwt.auth', 'actove']], function() {
    
    //Route for showing all Record
    Route::get('/records', 'RecordController@records');

    //Route for store record
    Route::post('/records', 'RecordController@storeRecord');
});
