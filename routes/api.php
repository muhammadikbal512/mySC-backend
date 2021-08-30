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
Route::get('users/roles', 'UserController@create');

//Teams API
Route::post('/create/team', 'TeamController@addTeam');
Route::get('/teams', 'TeamController@index');

Route::post('/add/team/{id}', 'TeamController@joinTeam');




Route::group(['prefix'=>'user','middleware'=>['jwt.auth', 'active']], function() {

    //Route for get User by ID
    Route::get('/user/{id}', 'UserController@getUserId');
    

    //Route for showing Dosen dropdown for Records
    Route::get('/records/dosen', 'RecordController@test');

    //Route for showing SC User Active
    Route::get('/records/sc', 'RecordController@recordSc');

    //Route for store record
    Route::post('/records', 'RecordController@storeRecord');

    //Route for showing userHistory Pending
    Route::get('/records/show/pending', 'RecordController@userPending');

    //Route for showing userHistory Pending
    Route::get('/records/show/approved', 'RecordController@userApproved');

    //Route for showing userHistory Pending
    Route::get('/records/show/rejected', 'RecordController@userRejected');

    //Route for show Request Redeem AIC
    Route::get('/records/redeemsc', 'AIController@showRecord');




    //GiveSCRoute
    Route::get('/records/givesc', 'GivescController@allRecord');
    Route::post('/givesc/{id}/feedbackApprove', 'RecordController@feedbackApproveGave');



    /*
|--------------------------------------------------------------------------
| Experience Route
|--------------------------------------------------------------------------
*/
//Get Exp for User Active
    Route::get('/experience','ExperienceController@exp')->middleware('jwt.auth','active');

    //Get Exp for Specific User
    Route::get('/experience/user/{id}','ExperienceController@expId')->middleware('jwt.auth','active');

    //Get A Progress Level Up
    Route::get('/experience/user/{id}/progress','ExperienceController@progress');

    //Get a Total SC and Total AIC
    Route::get('/experience/user', 'ExperienceController@exp');

    Route::get('/experience/sc', 'ExperienceController@claimSC');

    Route::get('/experience/mahasiswa', 'ExperienceController@allUser');

    Route::post('/experience/dosen/{id}', 'ExperienceController@Dosen');

    Route::post('/experience/team/{id}', 'ExperienceController@Team');

    //Get All Level Record
    Route::get('/level','LevelController@index');

    //Get All Level Record
    Route::get('/level/all','LevelController@userAll');


    //AIC
    Route::post('/aic/store', 'AIController@storeRecord');
    //Badge
    Route::get('/badge', 'ExperienceController@badge');

});

Route::group(['prefix'=>'secretchamber','middleware'=>['jwt.auth', 'active']], function() {
    
    Route::delete('/user/delete/{id}', 'UserController@destroy');
    
    Route::get('/records/get/{id}', 'RecordController@recordScId'); 
    
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

    //Route for approve all Record
    Route::post('/records/all/feedbackApprove', 'RecordController@feedbackAllApprove');

     //Route for approve a Record
     Route::post('/records/{id}/feedbackApprove', 'RecordController@feedbackApprove');

     //Route for Disapprove a Record
     Route::post('/records/{id}/feedbackReject', 'RecordController@feedbackReject');

     //Route for showing AIC Submitted
    Route::get('/aic/show', 'AIController@allRecord');


    //show user for givesc
    Route::get('/show/mahasiswa', 'GivescController@getRecords');


    //GiveSC Route
    Route::post('/givesc/records', 'GivescController@GiveSC');


    Route::post('/claimaic/{id}/aic', 'AIController@claimAIC');
    Route::get('/claimed/aic', 'AIController@claimedAIC');
    Route::get('/redeemed/aic', 'AIController@allRecordRedeemed');
    Route::post('/claimaic/all', 'AIController@claimAllAIC');

    

});

