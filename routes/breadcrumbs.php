<?php
use Illuminate\Support\Facades\Auth;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// Dashboard ---------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('dashboard', function ($breadcrumbs) {
	$breadcrumbs->push('Dashboard', route(Auth::getDefaultDriver() . '.dashboard.index'));
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