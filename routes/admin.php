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


Route::group(['namespace' => 'Admin', 'middleware' => ['check_permit', 'revalidate']], function () {
	
	/* Dashboard */
	Route::get('/', 'PagesController@dashboard')->name('dashboard.index');
	Route::get('/dashboard', 'PagesController@dashboard')->name('dashboard.index');
	
	/* User */
	Route::get('users/listing', 'UsersController@listing')->name('users.listing');
	Route::get('users/unde_review', 'UsersController@unde_review')->name('users.unde-review');
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
	Route::resource('transaction-lists', 'TrasactionListController');

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
Route::get('genderprlisting', 'Admin\PagesController@gender_pr_listing')->name('genderprlisting');
Route::get('locationprlisting', 'Admin\PagesController@location_pr_listing')->name('locationprlisting');

//User Exception
Route::get('users-error-listing', 'Admin\ErrorController@listing')->name('error.listing');
//Chart routes
Route::get('register-users-chart', 'Admin\ChartController@getRegisterUser')->name('users.registerchart');
Route::get('active-deactive-users-chart', 'Admin\ChartController@getActiveDeactiveUser')->name('users.activeDeactiveChart');

Route::post('check-email', 'UtilityController@checkEmail')->name('check.email');
Route::post('check-contact', 'UtilityController@checkContact')->name('check.contact');
Route::get('api-translate', 'UtilityController@translate');

Route::post('summernote-image-upload', 'Admin\SummernoteController@imageUpload')->name('summernote.imageUpload');
Route::post('summernote-media-image', 'Admin\SummernoteController@mediaDelete')->name('summernote.mediaDelete');

Route::post('check-title', 'UtilityController@checkTitle')->name('check.title');
Route::post('profile/check-password', 'UtilityController@profilecheckpassword')->name('profile.check-password');
