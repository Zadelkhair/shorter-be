<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// login
Route::post('/login', [AuthController::class, 'login']);
// register
Route::post('/register', [AuthController::class, 'register']);


Route::group(['middleware' => ['auth:sanctum']], function () {

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // get user urls
    Route::get('/user/{id}/urls', [UrlController::class, 'userUrls']);

    // update url
    Route::put('/urls/{id}', [UrlController::class, 'update']);

    // url shortener
    Route::post('/shorten', [UrlController::class, 'shorten']);

    // get all urls
    Route::get('/urls', [UrlController::class, 'index']);

    // delete url
    Route::delete('/urls/{id}', [UrlController::class, 'destroy']);

    // load views
    Route::get('/urls/{id}/views', [UrlController::class, 'views']);

});

// prefix: /nonloggedinuser
Route::group(['prefix' => 'nonloggedinuser'], function () {

    // url shortener
    Route::post('/shorten', [UrlController::class, 'shorten']);

    // get all urls
    Route::get('/urls', [UrlController::class, 'index']);

    // delete url
    Route::delete('/urls/{id}', [UrlController::class, 'destroy']);

    // load views
    Route::get('/urls/{id}/views', [UrlController::class, 'views']);

});

Route::group(['middleware' => ['auth:sanctum','role:admin']], function () {

    // get url
    Route::get('/urls/{id}', [UrlController::class, 'show']);

});
