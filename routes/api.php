<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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

// get products
Route::get('/products', [ProductController::class, 'index']);

// auth middleware
Route::group(['middleware' => ['auth:sanctum']], function () {

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);

});

Route::group(['middleware' => ['auth:sanctum','role:admin']], function () {

    // create product
    Route::post('/products', [ProductController::class, 'store']);
    // update product
    Route::put('/products/{id}', [ProductController::class, 'update']);
    // delete product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

});

