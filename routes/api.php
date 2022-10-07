<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiLanguage;
use App\Http\Controllers\api\v1\ { AuthenticationController, GeneralController, UserController, LikeController, TwillioController, ChatController, MatchController, SearchController, BlockController, VerificationController, ProfileController, DiscoveryController, FilterController, HomeController, PaymentController, SubscriptionController };

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
Route::group(['namespace' => 'v1', 'prefix' => 'v1'], function () {
    // Authentication
    Route::post('login', [AuthenticationController::class,'login'])->name('api.user.login');
    Route::post('social/login', [AuthenticationController::class,'socialLogin'])->name('api.social-login');
    Route::any('generate-checksum', [AuthenticationController::class,'generateChecksum'])->name('api.generate-checksum');
    Route::post('user/set-profile', [AuthenticationController::class,'setProfile'])->name('api.user.set-profile');

    // Listing
    Route::post('app-status', [GeneralController::class,'appStatus'])->name('api.app-status');
    Route::post('get/countries',[GeneralController::class,'getCountries'])->name('api.get-countries');
    Route::post('get/cms-pages',[GeneralController::class,'getCmsPages'])->name('api.get-cms-pages');
    Route::post('get/locations',[GeneralController::class,'getLocations'])->name('api.get-locations');
    Route::post('get/locations-trans',[GeneralController::class,'getLocationsTrans'])->name('api.get-locations-trans');
    Route::post('get/interests',[GeneralController::class,'getInterests'])->name('api.get-interests');
    Route::post('get/personalities',[GeneralController::class,'getPersonalities'])->name('api.get-personalities');
    Route::post('get/faqs',[GeneralController::class,'getFaqs'])->name('api.get-faqs');


    Route::post('set/check-location',[GeneralController::class,'setLocation'])->name('api.check-location');



    // Route::post('get/languages', [GeneralController::class,'getLanguages'])->name('api.get-languages');

    // General Profile Listing
    Route::post('profile/get-details', [GeneralController::class,'getProfileDetails'])->name('api.profile.get-details');

    // User
    Route::post('user/common-age',[UserController::class,'getCommonAge'])->name('api.user.common-age');
    Route::post('user/get-device-token', [GeneralController::class,'getDeviceToken'])->name('api.user.get-device-token');

    // Third Party
    Route::post('image/moderation', [GeneralController::class,'checkImageModeration'])->name('api.image.moderation');
    
    // Send Chat Notification
    Route::post('chat/send-push/{chatmessage}/{message?}', [ChatController::class,'sendChatPush'])->name('chat.send-push');
});

Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'middleware' => ['auth:sanctum','checkapiuser']], function () {
    Route::post('logout',[AuthenticationController::class,'logout'])->name('api.user.logout'); 

    // Home List
    Route::post('user/get-list', [HomeController::class,'getHomeFeeds'])->name('api.user.get-list')->middleware('change_language');

    // User
    Route::post('user/set-full-profile', [ProfileController::class,'setFullProfile'])->name('api.user.set-fill-profile');
    Route::post('user/set-interest', [ProfileController::class,'setInterest'])->name('api.user.set-interest');
    Route::post('user/set-media', [ProfileController::class,'setMedia'])->name('api.user.set-media');
    Route::post('user/profile-report',[UserController::class,'storeProfileReport'])->name('api.user.profile-report');
    Route::post('user/set-latlong',[UserController::class,'storeLatLong'])->name('api.user.set-latlong');

    // Filter
    Route::post('user/profile-filters', [FilterController::class,'getUsersByFilter'])->name('api.user.profile-filters');

    // My Profile
    Route::post('user/my-profile', [UserController::class,'getMyProfile'])->name('api.user.my-profile')->middleware('change_language');
    Route::post('user/get-profile', [UserController::class,'getProfile'])->name('api.user.get-profile');
    Route::post('user/delete-account', [UserController::class,'deletAccount'])->name('api.user.delete-account');

    // Discovery
    Route::post('discovery/set-location', [DiscoveryController::class,'setDiscoveryLocation'])->name('api.discovery.set-location');
    Route::post('discovery/set-detail', [DiscoveryController::class,'setDiscoveryDetail'])->name('api.discovery.set-detail');
    Route::post('discovery/get-detail', [DiscoveryController::class,'getDiscoveryDetail'])->name('api.discovery.get-detail');

    // Verify Details
    Route::post('verify/upload-detail', [VerificationController::class,'uploadVerifyDetail'])->name('api.verify.upload-detail');
    Route::post('verify/contact-no', [VerificationController::class,'verifyContactNumber'])->name('api.verify.contact-no');
    Route::post('verify/verify-email', [VerificationController::class,'verifyEmail'])->name('api.verify.verify-email');
    Route::post('verify/get-details', [VerificationController::class,'getVerifyDetails'])->name('api.verify.get-details');

    // Block/UnBlock
    Route::post('user/block-list',[BlockController::class,'blockList'])->name('api.user.block-list');
    Route::post('user/block-unblock',[BlockController::class,'blockUnblockProfile'])->name('api.user.block-unblock');

    // Like / DisLike
    Route::post('user/add-like', [LikeController::class,'addNewLike'])->name('api.user.add-like');
    Route::post('user/add-dislike', [LikeController::class,'addNewDisLike'])->name('api.user.add-dislike');
    Route::post('user/get-likes', [LikeController::class,'getLikes'])->name('api.user.get-likes');

    // Match
    Route::post('match/new-matches',[MatchController::class,'getNewMatches'])->name('api.new-matches');
    Route::post('match/remove-match',[MatchController::class,'removeMatch'])->name('api.remove-match');

    //UPDATE IS-CONNECTED FOR SYSTEM MATCH TABLE.
    Route::post('match/system-match-connected',[MatchController::class,'setIsConnectedForSystemMatch'])->name('api.system-match-connected');

    // Search Match/Chat
    // Route::post('search/match-chat',[SearchController::class,'searchMatchAndChat'])->name('api.search.match-chat');

    // Razorpay Android
    Route::post('get/subscription-plans',[PaymentController::class,'getSubscriptionPlans'])->name('api.get-subscription-plans');
    Route::post('payment/create-order',[PaymentController::class,'createOrder'])->name('api.payment.create-order');
    Route::post('payment/varify-signature',[PaymentController::class,'verifySignature'])->name('api.payment.varify-signature');

    // subscription 
    Route::post('subscriptions/ios',[SubscriptionController::class,'buyIosSubscription'])->name('api.subscriptions-ios');
    Route::post('subscriptions/ios-restore',[SubscriptionController::class,'restoreSubscription'])->name('api.subscriptions-ios-restore');
    Route::post('user/subscriptions-details',[SubscriptionController::class,'getUserSubDetails'])->name('api.user.subscriptions-details');

    // Twillio Call
    Route::post('twillio/create-access-token',[TwillioController::class,'createAccessToken'])->name('api.twillio.create-access-token');
    Route::post('twillio/get-call-log',[TwillioController::class,'getCallLog'])->name('api.twillio.get-call-log');
    Route::post('twillio/store-call-log',[TwillioController::class,'storeCallLog'])->name('api.twillio.store-call-log');
    Route::post('twillio/receiver-detail',[TwillioController::class,'getReceiverDetail'])->name('api.twillio.receiver-detail');

    // Socket Chat
    Route::post('chat/create-room', [ChatController::class,'createChatRoom'])->name('chat.create-room');
    Route::post('chat/get-rooms', [ChatController::class,'getChatRooms'])->name('chat.get-rooms'); 
    Route::post('chat/delete-room', [ChatController::class,'deleteChatRoom'])->name('chat.delete-room');
    Route::post('chat/get-messages', [ChatController::class,'getChatMessages'])->name('chat.get-messages');

    // Device Token
    Route::post('user/add-device-token', [GeneralController::class,'storeDeviceToken'])->name('api.user.add-device-token');
    Route::post('notification/update-status',[UserController::class,'readNotifications'])->name('api.notification.update-status');

    // AWS S3 STORAGE
    Route::post('aws/generate-url', [GeneralController::class,'generateAwsUrl'])->name('aws.generate-url');

    /******************************************************** EXTRA ************************************************************/

    /* Twillio Testing Apis (Not Used Right Now) */
    /* Connect with Twilio */
    /*
    Route::post('twillio/connect', [TwillioController::class,'connectWithTwilio'])->name('api.twillio.connect');
    Route::post('twillio/make-call', [TwillioController::class,'makeCall'])->name('api.twillio.make-call');
    Route::post('twillio/receive-call', [TwillioController::class,'ReceiveCall'])->name('api.twillio.receive-call');
  
    Route::post('/twillio/create-api-key',[TwillioController::class,'createApiKey'])->name('api.twillio.create-api-key');
    Route::post('/twillio/outgoing-app-sid',[TwillioController::class,'getOutgoingAppSid'])->name('api.twillio.outgoing-app-sid');
    */
});