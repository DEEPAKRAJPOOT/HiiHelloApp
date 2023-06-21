<?php
use Illuminate\Support\Facades\Auth;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// Dashboard ---------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('dashboard', function ($breadcrumbs) {
	$breadcrumbs->push('Dashboard', route(Auth::getDefaultDriver() . '.dashboard.index'));
});



	Breadcrumbs::register('dashboard_update', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Edit dashboard data',route(Auth::getDefaultDriver().'.dashboard.edit'));
	});

// Users -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('users_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Users', route(Auth::getDefaultDriver().'.users.index'));
	});

	// Quick Links
	Breadcrumbs::register('quick_link', function ($breadcrumbs) {
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('Mange Quick Link', route('admin.quickLink'));
	});

	// Profile
	Breadcrumbs::register('my_profile', function ($breadcrumbs) {
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('Manage Account', route('admin.profile-view'));
	});

	Breadcrumbs::register('users_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('users_list');
	    $breadcrumbs->push('Add New User', route(Auth::getDefaultDriver().'.users.create'));
	});

	Breadcrumbs::register('users_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('users_list');
	    $breadcrumbs->push('Edit User', route(Auth::getDefaultDriver().'.users.edit', $id));
	});

	Breadcrumbs::register('users_view', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('users_list');
		$breadcrumbs->push('View User', route('admin.users.edit', $id));
	});

// Role Management -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('roles_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Roles', route(Auth::getDefaultDriver().'.roles.index'));
	});
	Breadcrumbs::register('roles_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('roles_list');
	    $breadcrumbs->push('Add New Role', route(Auth::getDefaultDriver().'.roles.create'));
	});
	Breadcrumbs::register('roles_update', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('roles_list');
		$breadcrumbs->push('Edit Role', route('admin.roles.edit', $id));
	});


// Passion Management -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('passions_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Passions', route(Auth::getDefaultDriver().'.passions.index'));
	});
	Breadcrumbs::register('passions_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('passions_list');
	    $breadcrumbs->push('Add New Passion', route(Auth::getDefaultDriver().'.passions.create'));
	});
	Breadcrumbs::register('passions_update', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('passions_list');
		$breadcrumbs->push('Edit Passion', route('admin.passions.edit', $id));
	});


	// countries -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('countries_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Countries', route(Auth::getDefaultDriver().'.countries.index'));
	});
	Breadcrumbs::register('countries_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('countries_list');
	    $breadcrumbs->push('Add New Country', route(Auth::getDefaultDriver().'.countries.create'));
	});

	Breadcrumbs::register('countries_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('countries_list');
	    $breadcrumbs->push('Edit Country', route(Auth::getDefaultDriver().'.countries.edit', $id));
	});


	// Profile Details -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('profile_details_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Profile Details', route(Auth::getDefaultDriver().'.profile-details.index'));
	});
	Breadcrumbs::register('profile_details_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('profile_details_list');
	    $breadcrumbs->push('Add New Profile Detail', route(Auth::getDefaultDriver().'.profile-details.create'));
	});

	Breadcrumbs::register('profile_details_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('profile_details_list');
	    $breadcrumbs->push('Edit Profile Detail', route(Auth::getDefaultDriver().'.profile-details.edit', $id));
	});


	// Interests Management -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('interests_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Interests', route(Auth::getDefaultDriver().'.interests.index'));
	});
	Breadcrumbs::register('interests_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('interests_list');
	    $breadcrumbs->push('Add New Interest', route(Auth::getDefaultDriver().'.interests.create'));
	});
	Breadcrumbs::register('interests_update', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('interests_list');
		$breadcrumbs->push('Edit Interest', route('admin.interests.edit', $id));
	});


	// Locations Management -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('locations_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Locations', route(Auth::getDefaultDriver().'.locations.index'));
	});
	Breadcrumbs::register('locations_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('locations_list');
	    $breadcrumbs->push('Add New Location', route(Auth::getDefaultDriver().'.locations.create'));
	});
	Breadcrumbs::register('locations_update', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('locations_list');
		$breadcrumbs->push('Edit Location', route('admin.locations.edit', $id));
	});


	// Profile Reports Management -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('profile_reports_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Profile Reports', route(Auth::getDefaultDriver().'.profile-reports.index'));
	});
	Breadcrumbs::register('profile_reports_update', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('profile_reports_list');
		$breadcrumbs->push('Edit Profile Report', route('admin.profile-reports.edit', $id));
	});
	Breadcrumbs::register('profile_reports_view', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('profile_reports_list');
		$breadcrumbs->push('View Profile Report', route('admin.profile-reports.edit', $id));
	});


	// Faqs -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('faqs_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Faqs', route(Auth::getDefaultDriver().'.faqs.index'));
	});
	Breadcrumbs::register('faqs_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('faqs_list');
	    $breadcrumbs->push('Add New Faq', route(Auth::getDefaultDriver().'.faqs.create'));
	});

	Breadcrumbs::register('faqs_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('faqs_list');
	    $breadcrumbs->push('Edit Faq', route(Auth::getDefaultDriver().'.faqs.edit', $id));
	});

	// states -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('states_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('States', route(Auth::getDefaultDriver().'.states.index'));
	});
	Breadcrumbs::register('states_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('states_list');
	    $breadcrumbs->push('Add New State', route(Auth::getDefaultDriver().'.states.create'));
	});

	Breadcrumbs::register('states_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('states_list');
	    $breadcrumbs->push('Edit State', route(Auth::getDefaultDriver().'.states.edit', $id));
	});



	// Personality Types -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('personality_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Personality Types', route(Auth::getDefaultDriver().'.personalities.index'));
	});
	Breadcrumbs::register('personality_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('personality_list');
	    $breadcrumbs->push('Add New Personality Type', route(Auth::getDefaultDriver().'.personalities.create'));
	});
	Breadcrumbs::register('personality_update', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('personality_list');
		$breadcrumbs->push('Edit Personality Type', route('admin.personalities.edit', $id));
	});

	// cities -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('cities_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Cities', route(Auth::getDefaultDriver().'.cities.index'));
	});
	Breadcrumbs::register('cities_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('cities_list');
	    $breadcrumbs->push('Add New City', route(Auth::getDefaultDriver().'.cities.create'));
	});

	Breadcrumbs::register('cities_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('cities_list');
	    $breadcrumbs->push('Edit City', route(Auth::getDefaultDriver().'.cities.edit', $id));
	});


	// Push Notification -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('push_notification_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Send Push Notifiaction', route(Auth::getDefaultDriver().'.push-notification.create'));
	});


	// Email Notification -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('email_notification_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Send Email Notifiaction', route(Auth::getDefaultDriver().'.email-notification.create'));
	});


	// Subscription Plans -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('subscription_plans_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Subscription Plans', route(Auth::getDefaultDriver().'.subscription-plans.index'));
	});
	Breadcrumbs::register('subscription_plans_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('subscription_plans_list');
	    $breadcrumbs->push('Add New Subscription Plan', route(Auth::getDefaultDriver().'.subscription-plans.create'));
	});

	Breadcrumbs::register('subscription_plans_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('subscription_plans_list');
	    $breadcrumbs->push('Edit Subscription Plan', route(Auth::getDefaultDriver().'.subscription-plans.edit', $id));
	});

	// Subscriptions ------------------------------------------------------------------
	Breadcrumbs::register('subscription_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Subscriptions', route(Auth::getDefaultDriver().'.subscription-lists.index'));
	});

	Breadcrumbs::register('subscription_view', function($breadcrumbs,$id)
	{
		$breadcrumbs->parent('subscription_list');
		$breadcrumbs->push('Subscription View', route('admin.subscription-lists.show', $id));
	});

	// Coupons ------------------------------------------------------------------
	Breadcrumbs::register('coupons_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Coupons', route(Auth::getDefaultDriver().'.coupons.index'));
	});

	Breadcrumbs::register('coupon_view', function($breadcrumbs,$id)
	{
		$breadcrumbs->parent('coupons_list');
		$breadcrumbs->push('Coupon Details', route('admin.coupons.show', $id));
	});

	Breadcrumbs::register('coupon_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('coupons_list');
	    $breadcrumbs->push('Add New Coupon', route(Auth::getDefaultDriver().'.coupons.create'));
	});

	Breadcrumbs::register('coupon_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('coupons_list');
	    $breadcrumbs->push('Edit Coupon', route(Auth::getDefaultDriver().'.coupons.edit', $id));
	});

	// Coupon Vendors------------------------------------------------------------------
	Breadcrumbs::register('coupon_vendors_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Coupon Vendors', route(Auth::getDefaultDriver().'.coupon-vendors.index'));
	});

	Breadcrumbs::register('coupon_vendor_view', function($breadcrumbs,$id)
	{
		$breadcrumbs->parent('coupon_vendors_list');
		$breadcrumbs->push('Vendor Details', route('admin.coupon-vendors.show', $id));
	});

	Breadcrumbs::register('coupon_vendor_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('coupon_vendors_list');
	    $breadcrumbs->push('Add New Coupon Vendor', route(Auth::getDefaultDriver().'.coupon-vendors.create'));
	});

	Breadcrumbs::register('coupon_vendor_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('coupon_vendors_list');
	    $breadcrumbs->push('Edit Coupon Vendor', route(Auth::getDefaultDriver().'.coupon-vendors.edit', $id));
	});

	//Coupon Users -------------------------------------------------------------------------------------
	Breadcrumbs::register('coupon_users_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Coupon Users', route(Auth::getDefaultDriver().'.coupon-users.index'));
	});

	//User matches
	Breadcrumbs::register('user_matches',function($breadcrumbs){
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('User Matches',route(Auth::getDefaultDriver().'.user-matches.index'));
	});

	Breadcrumbs::register('user_matches_view',function($breadcrumbs,$id){
		$breadcrumbs->parent('user_matches');
	    $breadcrumbs->push('User Match Details',route(Auth::getDefaultDriver().'.user-matches.show',$id));
	});

	//Colleges
	Breadcrumbs::register('colleges_list',function($breadcrumbs){
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Colleges',route(Auth::getDefaultDriver().'.colleges.index'));
	});

	Breadcrumbs::register('colleges_view', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('colleges_list');
		$breadcrumbs->push('View College', route('admin.colleges.edit', $id));
	});

	Breadcrumbs::register('college_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('colleges_list');
	    $breadcrumbs->push('Add New College', route(Auth::getDefaultDriver().'.colleges.create'));
	});

	Breadcrumbs::register('college_update', function($breadcrumbs, $id)
	{
		$breadcrumbs->parent('colleges_list');
	    $breadcrumbs->push('Edit College', route(Auth::getDefaultDriver().'.colleges.edit', $id));
	});

	//System Chat
	Breadcrumbs::register('system_chats_list',function($breadcrumbs){
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('System Chats',route(Auth::getDefaultDriver().'.system-chat.index'));
	});

	Breadcrumbs::register('system_chats_view', function ($breadcrumbs, $id)
	{
		$breadcrumbs->parent('system_chats_list');
		$breadcrumbs->push('View Chat', route('admin.system-chat.edit', $id));
	});

	Breadcrumbs::register('system_chat_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('system_chats_list');
	    $breadcrumbs->push('Send New Message', route(Auth::getDefaultDriver().'.system-chat.create'));
	});

	// Call Logs ------------------------------------------------------------------
	Breadcrumbs::register('call_log_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('Call Logs', route(Auth::getDefaultDriver().'.call-logs.index'));
	});

	Breadcrumbs::register('call_log_view', function($breadcrumbs,$id)
	{
		$breadcrumbs->parent('call_log_list');
		$breadcrumbs->push('Call Logs View', route('admin.call-logs.show', $id));
	});

	//Transactions -------------------------------------------------------------------------------------
	Breadcrumbs::register('transaction_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Transactions', route(Auth::getDefaultDriver().'.transaction-lists.index'));
	});

	Breadcrumbs::register('transaction_view', function($breadcrumbs,$id)
	{
		$breadcrumbs->parent('transaction_list');
		$breadcrumbs->push('Transaction View', route('admin.transaction-lists.show', $id));
	});

	// App Details -------------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('app_detail_create', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
	    $breadcrumbs->push('Add App Details', route(Auth::getDefaultDriver().'.app-details.create'));
	});

	// CMS Pages ---------------------------------------------------------------------------------------------------------------------------------------------------
	Breadcrumbs::register('cms_list', function ($breadcrumbs) {
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('CMS Pages', route('admin.pages.index'));
	});
	Breadcrumbs::register('cms_update', function ($breadcrumbs, $id) {
		$breadcrumbs->parent('cms_list');
		$breadcrumbs->push('Edit CMS Page', route('admin.pages.edit', $id));
	});
	//site configuartion
	Breadcrumbs::register('site_setting', function ($breadcrumbs) {
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('Site Configuration', route('admin.settings.index'));
	});

	//site configuartion
	Breadcrumbs::register('usertree', function ($breadcrumbs) {
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('User Tree', route('admin.usertree'));
	});

	// Image Logs ------------------------------------------------------------------
	Breadcrumbs::register('image_log_list', function($breadcrumbs)
	{
		$breadcrumbs->parent('dashboard');
		$breadcrumbs->push('Image Moderation Logs', route('admin.image-logs.listing'));
	});