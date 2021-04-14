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


Route::post('/auth/callback/{provider}','AuthController@callback')->middleware('active');

Route::post('/auth/logout','AuthController@logout');
//Detail User
Route::post('/auth/user','AuthController@show')->middleware('jwt.auth');




// Route::get('login/google', 'Auth\LoginController@redirectToProvider');
// Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');
// Route::get('login/user/{user}', 'Auth\LoginController@retrieveUser');


Route::resource('users', 'UserController');



Route::group(['prefix'=>'user','middleware'=>['jwt.auth', 'active']], function() {
    //Route for showing all Records
    Route::get('/records', 'RecordController@allRecord');

    //Route for showing all Pending Records
    Route::get('/records/pending', 'RecordController@recordPending');

    //Route for showing all Verified Records
    Route::get('/records/verified', 'RecordController@recordVerified');
    
    //Route for showing all Denied Records
    Route::get('/records/denied', 'RecordController@recordDenied');

    //Route for store record
    Route::post('/records', 'RecordController@storeRecord');

    //Route for showing userHistory
    Route::get('/records/show/{id}', 'RecordController@userHistory');

    //Route for approve a Record
    Route::post('/records/{id}/feedback', 'RecordController@feedback');

    //Get A Progress Level Up
    Route::get('/experience/user/{id}/progress','ExperienceController@progress');

    //Get a Total SC and Total AIC
    Route::get('/experience/user/{id}', 'ExperienceController@exp');

    //Get All Level Record
    Route::get('/level','LevelController@index');

});
