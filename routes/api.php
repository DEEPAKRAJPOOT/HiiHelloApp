<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\AuthenticationController;
use App\Http\Controllers\api\v1\GeneralController;

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

Route::group(['namespace' => 'v1', 'prefix' => 'v1'], function () {
    // Authentication
    Route::post('login', [AuthenticationController::class,'login'])->name('api.user.login');
    Route::post('register', [AuthenticationController::class,'register'])->name('api.user.register');
    
    // Listing
    Route::post('get/languages', [GeneralController::class,'getLanguages'])->name('api.get.languages');
});