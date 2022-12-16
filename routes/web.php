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
// cache clear
Route::get('/cache', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('event:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize');
    dd("Cache is cleared");
});



Auth::routes(['register' => false, 'login' => false]);

// Twilio Voice Response
Route::post('/voice',[TwillioController::class,'voice']);
Route::get('/events',[TwillioController::class,'events'])->name('events');

Route::get('login', 'AdminAuth\LoginController@showLoginForm')->name('login');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id) {
    $user = User::findOrFail($id);
    $user->markEmailAsVerified();    
    return Redirect::to('https://www.hihelloapp.com/email-verify');
    //return redirect(route('home'));
})->name('verification.verify');

// Profile Details CSV Upload (Admin Side)
Route::post('profile-details/csv-upload','Admin\ProfileDetailController@csvUpload')->name('admin.profile-details.csv-upload');
Route::get('profile-details/sample-csv-download','Admin\ProfileDetailController@sampleCsvDownload')->name('admin.profile-details.sample-csv-download');
Route::get('users/csv-download', 'Admin\UsersController@csvDownload')->name('admin.users.csv-download');
Route::get('users/csv-download-unde-review', 'Admin\UsersController@csvDownloadUndeReview')->name('admin.users.csv-download-unde-review');
Route::get('subscription-lists/csv-download', 'Admin\SubscriptionListController@csvDownload')->name('admin.subscriptions.csv-download');
Route::get('transaction/csv-download', 'Admin\TrasactionListController@csvDownload')->name('admin.transactions.csv-download');
Route::get('profile-report/csv-download', 'Admin\ProfileReportController@csvDownload')->name('admin.profile-report.csv-download');
Route::get('call-log/csv-download', 'Admin\CallController@csvDownload')->name('admin.call-log.csv-download');

Route::get('location/csv-download', 'Admin\LocationController@csvDownload')->name('admin.location.csv-download');
// user data with location data sheet downlode
Route::get('location/user-location-csv-download', 'Admin\LocationController@userlocationcsvDownload')->name('admin.location.user-location-csv-download');
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

Route::group(['prefix' => 'webhook', 'as' => 'webhooks.', 'namespace' => 'WebHooks'], function() {

    // Web hook manage request
        // Route::any('ios', 'IosWebHook@manageAllRequest');
        // Route::any('android', 'AndroidWebHook@manageAllRequest');

    // Google Web Hooks
        // Route::any('android/test', 'AndroidWebHook@getTestRequest');

    // iOS Web Hooks
        Route::any('ios/test', 'IosWebHook@getTestRequest');

    // RazorPay (Android) Web Hooks
        Route::any('android', 'AndroidWebHook@storeDetails');
});

