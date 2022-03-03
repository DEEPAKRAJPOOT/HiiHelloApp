<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiLanguage;
use App\Http\Controllers\api\v1\ { AuthenticationController, GeneralController, UserController, LikeController, TwillioController, ChatController };

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
    Route::any('generate-checksum', [AuthenticationController::class,'generateChecksum'])->name('api.generate-checksum');
    Route::post('user/set-profile', [AuthenticationController::class,'setProfile'])->name('api.user.set-profile');
    
    // Listing
    Route::post('app-status', [GeneralController::class,'appStatus'])->name('api.app-status');
    Route::post('get/countries',[GeneralController::class,'getCountries'])->name('api.get-countries');
    Route::post('get/cms-pages',[GeneralController::class,'getCmsPages'])->name('api.get-cms-pages');
    Route::post('get/locations',[GeneralController::class,'getLocations'])->name('api.get-locations');
    Route::post('get/interests',[GeneralController::class,'getInterests'])->name('api.get-interests');
    Route::post('get/faqs',[GeneralController::class,'getFaqs'])->name('api.get-faqs');
    // Route::post('get/languages', [GeneralController::class,'getLanguages'])->name('api.get-languages');

    // User
    Route::post('user/get-profile', [UserController::class,'getProfile'])->name('api.user.get-profile');
    Route::post('user/common-age',[UserController::class,'getCommonAge'])->name('api.user.common-age');

    // Third Party Api
    Route::post('image/moderation', [GeneralController::class,'checkImageModeration'])->name('api.image.moderation');
});

Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('user/get-list', [UserController::class,'getUsersList'])->name('api.user.get-list');
    Route::post('user/profile-report',[UserController::class,'storeProfileReport'])->name('api.user.profile-report');

    // Like
    Route::post('user/add-like', [LikeController::class,'addNewLike'])->name('api.user.add-like');
    Route::post('user/get-likes', [LikeController::class,'getLikes'])->name('api.user.get-likes');

    // Twillio Api
    Route::post('/twillio/create-api-key',[TwillioController::class,'createApiKey'])->name('api.twillio.create-api-key');
    Route::post('/twillio/create-access-token',[TwillioController::class,'createAccessToken'])->name('api.twillio.create-access-token');
    Route::post('/twillio/create-service-resource',[TwillioController::class,'createServiceResource'])->name('api.twillio.create-service-resource');
    Route::post('/twillio/new-message-notification',[TwillioController::class,'newMessageNotification'])->name('api.twillio.new-message-notification');

    // Chat
    Route::post('chat/create-room', [ChatController::class,'createRoom'])->name('chat.create-room');
    Route::post('chat/get-rooms', [ChatController::class,'getChatRooms'])->name('chat.get-rooms'); 
    Route::post('chat/get-messages', [ChatController::class,'getChatMessages'])->name('chat.get-messages');

    // Device Token
    Route::post('user/add-device-token', [GeneralController::class,'storeDeviceToken'])->name('api.user.add-device-token');

    // AWS S3 STORAGE
    Route::post('aws/generate-url', [GeneralController::class,'generateAwsUrl'])->name('aws.generate-url');
});