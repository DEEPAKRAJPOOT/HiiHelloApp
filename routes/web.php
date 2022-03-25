<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\ { TwillioController };
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

Route::get('/','FrontendPagesController@index')->name('home');

Auth::routes(['register' => false, 'login' => false]);

// Twilio Voice Response
Route::post('/voice',[TwillioController::class,'voice']);

Route::get('login', 'AdminAuth\LoginController@showLoginForm')->name('login');

/* CMS Pages */
  Route::get('about-us', 'FrontendPagesController@about')->name('about.us');
  Route::get('terms-and-conditions', 'FrontendPagesController@terms')->name('terms');
  Route::get('privacy-policy', 'FrontendPagesController@privacy')->name('privacy.policy');
    
Route::group(['prefix' => 'admin'], function () {
  Route::get('login', 'AdminAuth\LoginController@showLoginForm')->name('admin.login');
  Route::post('login', 'AdminAuth\LoginController@login');
  Route::get('logout', 'AdminAuth\LoginController@logout')->name('admin.logout');

  Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.request');
  Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset')->name('admin.password.email');
  Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.reset');
  Route::get('/password/reset/{token}/{email?}', 'AdminAuth\ResetPasswordController@showResetForm');
});
