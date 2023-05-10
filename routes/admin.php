<?php
Route::group(['middleware' => ['revalidate']], function () {
	Route::get('/home', function () {
		return redirect(route('admin.dashboard.index'));
	})->name('home');

	// Profile
	Route::get('profile/', 'Admin\PagesController@profile')->name('profile-view');
	Route::post('profile/update', 'Admin\PagesController@updateProfile')->name('profile.update');
	Route::put('change/password', 'Admin\PagesController@updatePassword')->name('update-password');

	// Quick Link
	Route::get('quickLink', 'Admin\PagesController@quickLink')->name('quickLink');
	Route::post('link/update', 'Admin\PagesController@updateQuickLink')->name('update-quickLink');

	
	
});

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


Route::group(['namespace' => 'Admin', 'middleware' => ['check_permit', 'revalidate']], function () {

	/* Dashboard */
	// Route::get('/', 'PagesController@dashboard')->name('dashboard.index');
	Route::get('/dashboard', 'PagesController@dashboard')->name('dashboard.index');
	Route::get('dashboardupdate', 'PagesController@dashboardupdate')->name('dashboardupdate');
	
	/* User */
	Route::get('users/listing', 'UsersController@listing')->name('users.listing');
	Route::post('users/genderupdate', 'UsersController@gender_update')->name('users.genderupdate');
	Route::post('users/bulk_gender_update', 'UsersController@bulk_gender_update')->name('users.bulk_gender_update');
	Route::post('users/single_gender_update', 'UsersController@single_gender_update')->name('users.single_gender_update');
	Route::post('users/bulk_photo_verification', 'UsersController@bulk_photo_verification')->name('users.bulk_photo_verification');
	Route::post('users/bulk_email_verification', 'UsersController@bulk_email_verification')->name('users.bulk_email_verification');
	
	/*Route::get('unde_review/listing', 'UsersController@under_review_listing')->name('users.under-review-listing');
	Route::get('users/unde_review', 'UsersController@unde_review')->name('users.unde-review');
	Route::get('users/deleted', 'UsersController@deleted')->name('users.deleted');*/
	Route::resource('users', 'UsersController');


	/* Role Management */
	Route::get('roles/listing', 'AdminController@listing')->name('roles.listing');
	Route::resource('roles', 'AdminController');

	/* Passions Management */
	Route::get('passions/listing', 'PassionController@listing')->name('passions.listing');
	Route::resource('passions', 'PassionController');

	/* Country Management*/
	Route::get('countries/listing', 'CountryController@listing')->name('countries.listing');
	Route::resource('countries', 'CountryController');

	/* Profile Details*/
	Route::get('profile-details/listing', 'ProfileDetailController@listing')->name('profile-details.listing');
	Route::resource('profile-details', 'ProfileDetailController');

	/* Interests Management*/
	Route::get('interests/listing', 'InterestController@listing')->name('interests.listing');
	Route::resource('interests', 'InterestController');

	/* Locations Management*/
	Route::get('locations/listing', 'LocationController@listing')->name('locations.listing');
	Route::resource('locations', 'LocationController');

	/* Locations Management*/
	Route::get('profile-reports/listing', 'ProfileReportController@listing')->name('profile-reports.listing');
	Route::resource('profile-reports', 'ProfileReportController');

	/* Faqs*/
	Route::get('faqs/listing', 'FaqController@listing')->name('faqs.listing');
	Route::resource('faqs', 'FaqController');

	/* State Management*/
	Route::get('states/listing', 'StateController@listing')->name('states.listing');
	Route::resource('states', 'StateController');

	/* Personlaity Types Management*/
	Route::get('personalities/listing', 'PersonalityController@listing')->name('personalities.listing');
	Route::resource('personalities', 'PersonalityController');

	/* City Management*/
	Route::get('cities/listing', 'CityController@listing')->name('cities.listing');
	Route::resource('cities', 'CityController');

	/* Push Notification */
	Route::resource('push-notification', 'PushNotificationController');

	/* Subscription Plans*/
	Route::get('subscription-plans/listing', 'SubscriptionPlanController@listing')->name('subscription-plans.listing');
	Route::resource('subscription-plans', 'SubscriptionPlanController');

	/*subscriptions */
	Route::get('subscription-lists/listing', 'SubscriptionListController@listing')->name('subscription-lists.listing');
	Route::resource('subscription-lists', 'SubscriptionListController');

	/* transaction */
	Route::get('transaction-lists/listing', 'TrasactionListController@listing')->name('transaction-lists.listing');
	Route::post('transaction-lists/refund-transaction', 'TrasactionListController@refundTransaction')->name('transaction-lists.refund-transaction');
	Route::get('transaction/filters', 'TrasactionListController@filters')->name('transaction.filters');
	Route::resource('transaction-lists', 'TrasactionListController');

	/* Coupon Vendors */
	Route::get('coupon-vendors/listing', 'CouponVendorController@listing')->name('coupon-vendors.listing');
	Route::resource('coupon-vendors', 'CouponVendorController');

	/* Coupons */
	Route::get('coupons/listing', 'CouponController@listing')->name('coupons.listing');
	Route::resource('coupons', 'CouponController');

	/* Coupon Users */
	Route::get('coupon-users/listing', 'CouponUsersController@listing')->name('coupon-users.listing');
	Route::resource('coupon-users', 'CouponUsersController');

	/* App Details */
	Route::resource('app-details', 'AppDetailController');

	/* CMS Management*/
	Route::get('pages/listing', 'CmsPagesController@listing')->name('pages.listing');
	Route::resource('pages', 'CmsPagesController');

	/* Site Configuration */
	Route::get('settings', 'PagesController@showSetting')->name('settings.index');
	Route::post('change-setting', 'PagesController@changeSetting')->name('settings.change-setting');

	// call logs
	Route::get('call-logs/listing', 'CallController@listing')->name('call-logs.listing');
	Route::resource('call-logs', 'CallController');

});

Route::get('image-logs', 'Admin\ImageModerationController@index')->name('image-logs');
Route::get('image-logs/listing', 'Admin\ImageModerationController@listing')->name('image-logs.listing');

// user tree
Route::get('usertree/listing', 'Admin\UserTreeController@listing')->name('usertree.listing');
Route::post('usertree/filters', 'Admin\UserTreeController@filters')->name('usertree.filters');
Route::get('usertree/usermatchlisting', 'Admin\UserTreeController@usermatchlisting')->name('usertree.usermatchlisting');
// Route::resource('usertree', 'Admin\UserTreeController');
Route::get('/usertree', 'Admin\UserTreeController@index')->name('usertree');
Route::post('usertree/get_user_match_data', 'Admin\UserTreeController@get_user_match_data')->name('usertree.get_user_match_data');
Route::get('usertree/top_usertree', 'Admin\UserTreeController@top_usertree')->name('usertree.top_usertree');

// user apilog
Route::get('apilog/listing', 'Admin\ApiLogController@listing')->name('apilog.listing');
Route::get('/apilog', 'Admin\ApiLogController@index')->name('apilog');
Route::post('/apilog/get_single_apilog_data', 'Admin\ApiLogController@get_single_apilog_data')->name('apilog.get_single_apilog_data');

//User Exception
Route::get('users-error-listing', 'Admin\ErrorController@listing')->name('error.listing');
Route::get('gender-listing', 'Admin\PagesController@gender_listing')->name('gender.listing');
Route::get('location-listing', 'Admin\PagesController@location_listing')->name('location.listing');
// language list with no of users
Route::get('language-listing', 'Admin\PagesController@language_listing')->name('dashboard.languagelisting');
//Chart routes
Route::get('register-users-chart', 'Admin\ChartController@getRegisterUser')->name('users.registerchart');
Route::get('active-deactive-users-chart', 'Admin\ChartController@getActiveDeactiveUser')->name('users.activeDeactiveChart');

Route::post('check-email', 'UtilityController@checkEmail')->name('check.email');
Route::post('check-contact', 'UtilityController@checkContact')->name('check.contact');
Route::get('api-translate', 'UtilityController@translate');
Route::get('chk_female_subscriptions', 'UtilityController@chk_female_subscriptions');
Route::get('assign_user_city_lat_long', 'UtilityController@assign_user_city_lat_long');

Route::post('summernote-image-upload', 'Admin\SummernoteController@imageUpload')->name('summernote.imageUpload');
Route::post('summernote-media-image', 'Admin\SummernoteController@mediaDelete')->name('summernote.mediaDelete');

Route::post('check-title', 'UtilityController@checkTitle')->name('check.title');
Route::post('profile/check-password', 'UtilityController@profilecheckpassword')->name('profile.check-password');
Route::post('profile-reports/filters', 'Admin\ProfileReportController@filters')->name('profile-reports.filters');
Route::post('profilereports/get_user_report_data', 'Admin\ProfileReportController@get_user_report_data')->name('profilereports.get_user_report_data');
// user translations table hindi language translate manualy
Route::get('user-translations', 'Admin\PagesController@user_translations')->name('user.translations');

// delete all location if user is not used 
Route::get('deletelocation', 'Admin\PagesController@deletelocation')->name('user.deletelocation');
Route::get('locationTranslations', 'UtilityController@locationTranslations');
Route::get('Usertranslate', 'UtilityController@Usertranslate');
// Temporary Link - To be deleted anytime after 11 May 2023
Route::get('getOldCollegeUsersTemp',function(){
	set_time_limit(0);
	if(!Illuminate\Support\Facades\File::exists(public_path('files'))){
		Illuminate\Support\Facades\File::makeDirectory(public_path('files'));
	}
	$filename = public_path('files/OldCollegeUsers.csv');
	$handle   = fopen($filename,'w+');
	try{
		chmod($filename,0777);
	}catch(\Exception $e){}
	$users = \App\Models\User::whereNotNUll('university_id')->where('university_id','!=','')->get();
	fputcsv($handle,[
		'Id','',
		'Custom ID','',
		'Account ID','',
		'Email','',
		'Contact No','',
		'Old College Name',''
	]);
	fputcsv($handle,['','','','','','','','','','','']);
	foreach($users as $user){
		fputcsv($handle,[
			$user->id,'',
			$user->custom_id,'',
			$user->account_id,'',
			$user->email,'',
			$user->contact_no,'',
			$user->university ? ($user->university->profileDetailTranslation ? $user->university->profileDetailTranslation->value : 'N/A') : 'N/A',''
		]);
	}
	fclose($handle);
	return Illuminate\Support\Facades\Response::download($filename,'OldCollegeUsers.csv',[
		'Content-Type' => 'text/csv'
	]);
});