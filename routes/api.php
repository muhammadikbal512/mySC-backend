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


Route::get('users', 'UserController@index');
Route::post('users/create', 'UserController@store');

Route::get('users/dosen', 'UserController@getDosen');





Route::group(['prefix'=>'user','middleware'=>['jwt.auth', 'active']], function() {
    

    //Route for showing Dosen dropdown for Records
    // Route::get('/records/dosen', 'RecordController@test');

    //Route for store record
    Route::post('/records', 'RecordController@storeRecord');

    //Route for showing userHistory Pending
    Route::get('/records/show/pending', 'RecordController@userPending');

    //Route for showing userHistory Pending
    Route::get('/records/show/approved', 'RecordController@userApproved');

    //Route for showing userHistory Pending
    Route::get('/records/show/rejected', 'RecordController@userRejected');

    //Get A Progress Level Up
    Route::get('/experience/user/{id}/progress','ExperienceController@progress');

    //Get a Total SC and Total AIC
    Route::get('/experience/user', 'ExperienceController@exp');

    Route::get('/experience/sc', 'ExperienceController@claimSC');

    Route::get('/experience/aic', 'ExperienceController@claimAIC');

    //AIC
    Route::post('/aic/store', 'AIController@storeRecord');

    //Get All Level Record
    Route::get('/level','LevelController@index');

});

Route::group(['prefix'=>'secretchamber','middleware'=>['jwt.auth', 'active']], function() {
     //Route for showing all Pending Records
     Route::get('/records/pending', 'RecordController@recordPending');

     //Route for showing Pending Records by Id
     Route::get('/records/pending/{id}', 'RecordController@pendingId');
 
     //Route for showing all Verified Records
     Route::get('/records/verified', 'RecordController@recordVerified');
     
     //Route for showing all Denied Records
     Route::get('/records/rejected', 'RecordController@recordDenied');

     //Route for showing all Records
    Route::get('/records', 'RecordController@allRecord');

     //Route for approve a Record
     Route::post('/records/{id}/feedbackApprove', 'RecordController@feedbackApprove');

     //Route for Disapprove a Record
     Route::post('/records/{id}/feedbackReject', 'RecordController@feedbackReject');

     //Route for showing AIC Submitted
    Route::get('/aic/show', 'AIController@allRecord');

});

