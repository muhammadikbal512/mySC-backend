<?php

use Illuminate\Support\Facades\Route;
use App\Mail\ScNotif;
use Illuminate\Support\Facades\Mail;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/email', function() {
//     Mail::to('email@email.com')->send(new ScNotif());
//     return new ScNotif();
// });
