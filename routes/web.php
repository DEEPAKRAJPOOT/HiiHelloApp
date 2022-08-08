<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\ { TwillioController };
use Illuminate\Foundation\Auth\ { EmailVerificationRequest };
use Illuminate\Auth\Events\ { Verified };
use App\Models\ { User };
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
Auth::routes(['register' => false, 'login' => false]);

// Twilio Voice Response
Route::post('/voice',[TwillioController::class,'voice']);
Route::get('/events',[TwillioController::class,'events'])->name('events');

Route::get('login', 'AdminAuth\LoginController@showLoginForm')->name('login');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id) {
    $user = User::findOrFail($id);
    $user->markEmailAsVerified();
    
    return redirect(route('home'));
})->name('verification.verify');

// Profile Details CSV Upload (Admin Side)
Route::post('profile-details/csv-upload','Admin\ProfileDetailController@csvUpload')->name('admin.profile-details.csv-upload');
Route::get('profile-details/sample-csv-download','Admin\ProfileDetailController@sampleCsvDownload')->name('admin.profile-details.sample-csv-download');

/* CMS Pages */
  Route::get('about-us/{device?}', 'FrontendPagesController@about')->name('about.us');
  Route::get('terms-and-conditions/{device?}', 'FrontendPagesController@terms')->name('terms');
  Route::get('privacy-policy/{device?}', 'FrontendPagesController@privacy')->name('privacy.policy');
  Route::get('community-and-safety/{device?}', 'FrontendPagesController@communityAndSafety')->name('community.safety');
    
Route::get('/{device?}','FrontendPagesController@index')->name('home');

Route::group(['prefix' => 'admin'], function () {
  Route::get('login', 'AdminAuth\LoginController@showLoginForm')->name('admin.login');
  Route::post('login', 'AdminAuth\LoginController@login');
  Route::get('logout', 'AdminAuth\LoginController@logout')->name('admin.logout');

  Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.request');
  Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset')->name('admin.password.email');
  Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.reset');
  Route::get('/password/reset/{token}/{email?}', 'AdminAuth\ResetPasswordController@showResetForm');
});
