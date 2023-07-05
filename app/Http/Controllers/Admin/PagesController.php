<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\General\ChangePassword;
use App\Http\Requests\Admin\General\ProfileUpdate;
use App\Models\QuickLink;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\City;
use App\Models\Location;
use App\Models\LocationTranslation;
use App\Models\State;
use App\Models\Language;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanTranslation;
use App\Models\UserTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\DashboardData;
use DB;
use Artisan;
use Exception;

class PagesController extends Controller
{

    public function dashboard()
    {
        $user = Auth::user();
        if(($user->dashboard_type ?? '') == 'controlled'){
            return $this->controlledDashboard();
        }
        $location_result = array();
        $city_result = array(); 
        $subscription_result = array(); 
        $age_result = array(); 

        // analytic dashboard to get last recoad
        $dashboard_data = DB::table("analytic_dashboard")->orderBy("id","DESC")->first();
        $total_subscribed = $dashboard_data ? $dashboard_data->paid_users : 0;
        $total_unsubscribed = $dashboard_data ? $dashboard_data->non_paid_users : 0;
        $total_male = $dashboard_data ? $dashboard_data->male_users : 0;
        $total_female = $dashboard_data ? $dashboard_data->female_users : 0;
        $total_users = $dashboard_data ? $dashboard_data->total_users : 0;

        $user['Count'] = $dashboard_data ? number_format($total_users) : 0;
        $user['total_male'] = number_format($total_male);
        $user['total_female'] = number_format($total_female);
        $user['total_na_user'] = $dashboard_data ? number_format($dashboard_data->na_users) : 0;
        $user['total_subscribed'] = number_format($total_subscribed);
        $user['total_unsubscribed'] = number_format($total_unsubscribed);
        $user['created_at'] = Carbon::parse($dashboard_data->created_at)->format('d-m-Y h:i A');
        
        $subscription_plans = SubscriptionPlanTranslation::select("locale","subscription_plan_id","name","subscriptions.plan_id as plan_id","subscriptions.status as status",DB::raw("count(users.id) as total_users"),DB::raw("sum(subscriptions.amount) as total_amount"))
                                    ->join("subscriptions","subscriptions.plan_id","=","subscription_plan_translations.subscription_plan_id")
                                    ->join("users","users.id","=","subscriptions.user_id")
                                    ->where("subscriptions.status","active")
                                    ->whereNull("subscriptions.deleted_at")
                                    ->where("users.gender","Male")
                                    ->groupBy("subscription_plan_translations.id")
                                    ->where(['subscription_plan_translations.locale' => 'en'])
                                    ->get();

        $age_result[] = [
            'male_age_18_25'    => $dashboard_data ? number_format($dashboard_data->male_18_25) : 0,
            'male_age_18_25_pr' => $dashboard_data ? number_format($dashboard_data->male_18_25 * 100 / $total_male,2) : 0,
            'male_age_26_35'    => $dashboard_data ? number_format($dashboard_data->male_26_35) : 0,
            'male_age_26_35_pr' => $dashboard_data ? number_format($dashboard_data->male_26_35 * 100 / $total_male,2) : 0,
            'male_age_36_45'    => $dashboard_data ? number_format($dashboard_data->male_36_45) : 0,
            'male_age_36_45_pr' => $dashboard_data ? number_format($dashboard_data->male_36_45 * 100 / $total_male,2) : 0,
            'male_age_45'       => $dashboard_data ? number_format($dashboard_data->male_45) : 0,
            'male_age_45_pr'    => $dashboard_data ? number_format($dashboard_data->male_45 * 100 / $total_male,2) : 0,
            'female_age_18_25'  => $dashboard_data ? number_format($dashboard_data->female_18_25) : 0,
            'female_age_18_25_pr'  => $dashboard_data ? number_format($dashboard_data->female_18_25 * 100 / $total_female,2) : 0,
            'female_age_26_35'  => $dashboard_data ? number_format($dashboard_data->female_26_35) : 0,
            'female_age_26_35_pr'  => $dashboard_data ? number_format($dashboard_data->female_26_35 * 100 / $total_female,2) : 0,
            'female_age_36_45'  => $dashboard_data ? number_format($dashboard_data->female_36_45) : 0,
            'female_age_36_45_pr'  => $dashboard_data ? number_format($dashboard_data->female_36_45 * 100 / $total_female,2) : 0,
            'female_age_45'     => $dashboard_data ? number_format($dashboard_data->female_45) : 0,
            'female_age_45_pr'     => $dashboard_data ? number_format($dashboard_data->female_45 * 100 / $total_female,2) : 0,
        ];

        $user['PerDayCount'] = $dashboard_data ? number_format($dashboard_data->per_day_users) : 0;
        $user['PerWeekCount'] = $dashboard_data ? number_format($dashboard_data->per_week_users) : 0;
        $user['Per30DayCount'] = $dashboard_data ? number_format($dashboard_data->per_30_day_users) : 0;
       
        $total_phone_users = $dashboard_data->total_phone_users;
        $total_google_users = $dashboard_data->total_google_users;
        $total_facebook_users = $dashboard_data->total_facebook_users;
        $total_apple_users = $dashboard_data->total_apple_users;
        $total_otp_less_users = $dashboard_data->total_otp_less_users;
        
        $user['total_phone_users'] = $dashboard_data ? number_format($total_phone_users) : 0;
        $user['total_google_users'] = $dashboard_data ? number_format($total_google_users) : 0;
        $user['total_facebook_users'] = $dashboard_data ? number_format($total_facebook_users) : 0;
        $user['total_apple_users'] = $dashboard_data ? number_format($total_apple_users) : 0;
        $user['pr_phone_users'] = $total_phone_users ? number_format($total_phone_users * 100 / $total_users) : 0;
        $user['pr_google_users'] = $total_phone_users ? number_format($total_google_users * 100 / $total_users) : 0;
        $user['pr_facebook_users'] = $total_phone_users ? number_format($total_facebook_users * 100 / $total_users) : 0;
        $user['pr_apple_users'] = $total_phone_users ? number_format($total_apple_users * 100 / $total_users) : 0;
        
        $user['total_otp_less_users'] = $dashboard_data ? number_format($total_otp_less_users) : 0;
        $user['pr_otp_less_users'] = $total_phone_users ? number_format($total_otp_less_users * 100 / $total_users) : 0;

        $user['male_phone_verified'] = $dashboard_data ? number_format($dashboard_data->male_phone_verified) : 0;
        $user['male_phone_unverified'] = $dashboard_data ? number_format($dashboard_data->male_phone_unverified) : 0;
        $user['male_email_verified'] = $dashboard_data ? number_format($dashboard_data->male_email_verified) : 0;
        $user['male_email_unverified'] = $dashboard_data ? number_format($dashboard_data->male_email_unverified) : 0;
        $user['male_photo_verified'] = $dashboard_data ? number_format($dashboard_data->male_photo_verified) : 0;
        $user['male_photo_unverified'] = $dashboard_data ? number_format($dashboard_data->male_photo_unverified) : 0;
        $user['male_phone_unverified'] = $dashboard_data ? number_format($dashboard_data->male_phone_unverified) : 0;
        $user['male_account_verified'] = $dashboard_data ? number_format($dashboard_data->male_account_verified) : 0;
        $user['male_account_unverified'] = $dashboard_data ? number_format($dashboard_data->male_account_unverified) : 0;
        $user['female_phone_verified'] = $dashboard_data ? number_format($dashboard_data->female_phone_verified) : 0;
        $user['female_phone_unverified'] = $dashboard_data ? number_format($dashboard_data->female_phone_unverified) : 0;
        $user['female_email_verified'] = $dashboard_data ? number_format($dashboard_data->female_email_verified) : 0;
        $user['female_email_unverified'] = $dashboard_data ? number_format($dashboard_data->female_email_unverified) : 0;
        $user['female_photo_verified'] = $dashboard_data ? number_format($dashboard_data->female_photo_verified) : 0;
        $user['female_photo_unverified'] = $dashboard_data ? number_format($dashboard_data->female_photo_unverified) : 0;
        $user['female_account_verified'] = $dashboard_data ? number_format($dashboard_data->female_account_verified) : 0;
        $user['female_account_unverified'] = $dashboard_data ? number_format($dashboard_data->female_account_unverified) : 0;

        $user['location_result'] = [];
        $user['city_result'] = [];
        $user['subscription_result'] = $subscription_plans;
        $user['age_result'] = $age_result;
        $user['paid_users_pr'] = number_format($total_subscribed / $total_male * 100,2);
        $user['nonpaid_users_pr'] = number_format($total_unsubscribed / $total_male * 100,2);


        // echo "<pre>"; print_r($subscription_result); die();
        return view('admin.pages.general.dashboard', compact('user'))->with(['custom_title' => __('Dashboard')]);
    }

    public function controlledDashboard(){
        $dashboard_data['total_downloads'] = DashboardData::where('name','like','users_downloads_%')->sum('value');
        $dashboard_data['total_uninstalls'] = DashboardData::where('name','like','users_uninstalls_%')->sum('value');
        $dashboard_data['downloads_this_month'] = DashboardData::where('name','like','users_downloads_'.strtolower(date('Y_M')).'%')->sum('value');
        $dashboard_data['uninstalls_this_month'] = DashboardData::where('name','like','users_uninstalls_'.strtolower(date('Y_M')).'%')->sum('value');
        $dashboard_data['total_downloads_males'] = DashboardData::where('name','like','males_users_downloads_%')->sum('value');
        $dashboard_data['total_downloads_females'] = DashboardData::where('name','like','females_users_downloads_%')->sum('value');
        $dashboard_data['last_twelve_months_data'] = [];
        $current_date = now();
        for($i=0;$i<12;$i++){
            $year_month_string = strtolower($current_date->format('Y_M'));
            $dashboard_data['last_twelve_months_data'][$current_date->format('M Y')] = [
                'total_downloads' => DashboardData::where('name','like','users_downloads_'.$year_month_string.'%')->sum('value'),
                'male_downloads' => DashboardData::where('name','like','males_users_downloads_'.$year_month_string.'%')->sum('value'),
                'female_downloads' => DashboardData::where('name','like','females_users_downloads_'.$year_month_string.'%')->sum('value'),
                'organic_downloads' => DashboardData::where('name','users_downloads_'.$year_month_string.'_organic')->first()->value ?? 0,
                'paid_downloads' => DashboardData::where('name','users_downloads_'.$year_month_string.'_paid')->first()->value ?? 0,
                'referral_downloads' => DashboardData::where('name','users_downloads_'.$year_month_string.'_referrals')->first()->value ?? 0,
                'male_percentage' => DashboardData::where('name','users_percentages_'.$year_month_string.'_male')->first()->value ?? 0,
                'female_percentage' => DashboardData::where('name','users_percentages_'.$year_month_string.'_female')->first()->value ?? 0,
                'total_uninstalls' => DashboardData::where('name','users_uninstalls_'.$year_month_string)->first()->value ?? 0,
                'daily_time_male_subscribers' => DashboardData::where('name','users_daily_time_'.$year_month_string.'_male_subscribers')->first()->value ?? 'N/A',
                'daily_time_male_non_subscribers' => DashboardData::where('name','users_daily_time_'.$year_month_string.'_male_non_subscribers')->first()->value ?? 'N/A',
                'daily_time_female_users' => DashboardData::where('name','users_daily_time_'.$year_month_string.'_female_users')->first()->value ?? 'N/A',
                'logins_male_subscribers' => DashboardData::where('name','users_logins_'.$year_month_string.'_male_subscribers')->first()->value ?? 'N/A',
                'logins_male_non_subscribers' => DashboardData::where('name','users_logins_'.$year_month_string.'_male_non_subscribers')->first()->value ?? 'N/A',
                'logins_female_users' => DashboardData::where('name','users_logins_'.$year_month_string.'_female_users')->first()->value ?? 'N/A',
                'active_daily' => DashboardData::where('name','users_active_'.$year_month_string.'_daily')->first()->value ?? 0,
                'active_monthly' => DashboardData::where('name','users_active_'.$year_month_string.'_monthly')->first()->value ?? 0,
                'notifications_sent_email' => DashboardData::where('name','notifications_sent_'.$year_month_string.'_email')->first()->value ?? 0,
                'notifications_sent_sms' => DashboardData::where('name','notifications_sent_'.$year_month_string.'_sms')->first()->value ?? 0,
                'notifications_sent_in_app' => DashboardData::where('name','notifications_sent_'.$year_month_string.'_in_app')->first()->value ?? 0,
                'notifications_clicked_percentages_email' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_email')->first()->value ?? 0,
                'notifications_clicked_percentages_sms' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_sms')->first()->value ?? 0,
                'notifications_clicked_percentages_in_app' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_in_app')->first()->value ?? 0,
                'users_retention_d1' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_email')->first()->value ?? 0,
                'users_retention_d7' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_sms')->first()->value ?? 0,
                'users_retention_d30' => DashboardData::where('name','notifications_clicked_percentages_'.$year_month_string.'_in_app')->first()->value ?? 0,
                'paid_users_weekly' => DashboardData::where('name','paid_users_'.$year_month_string.'_weekly')->first()->value ?? 0,
                'paid_users_monthly' => DashboardData::where('name','paid_users_'.$year_month_string.'_monthly')->first()->value ?? 0,
                'paid_users_half_yearly' => DashboardData::where('name','paid_users_'.$year_month_string.'_half_yearly')->first()->value ?? 0,
                'paid_users_yearly' => DashboardData::where('name','paid_users_'.$year_month_string.'_yearly')->first()->value ?? 0,
                'revenue_by_weekly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_weekly')->first()->value ?? 0) * 49,
                'revenue_by_monthly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_monthly')->first()->value ?? 0) * 99,
                'revenue_by_half_yearly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_half_yearly')->first()->value ?? 0) * 299,
                'revenue_by_yearly' => intval(DashboardData::where('name','paid_users_'.$year_month_string.'_yearly')->first()->value ?? 0) * 399,
            ];
            $current_date = $current_date->subMonth();
            if($current_date->format('Ym') == '202209'){
                break;
            }
        }
        $dashboard_data['last_twelve_months_data'] = array_reverse($dashboard_data['last_twelve_months_data']);
        return view('admin.pages.dashboard.view-controlled',$dashboard_data)->with(['custom_title'=>__('Dashboard')]);
    }

    public function dashboardupdate()
    {
        ini_set('max_execution_time',3600);
        set_time_limit(3600);
        try{
            (new \App\Console\Commands\AdminDashboard)->handle();
            flash('Dashboard details updated successfully!')->success();
        }catch(Exception $e){
            flash('Unable to update dashboard details. Error: '.$e->getMessage())->error();
        }
        return redirect()->route('admin.dashboard.index');
    }
    public function profile()
    {
        $user = Auth::user();
        // dd($user);
        return view('admin.pages.general.profile', compact('user'))->with(['custom_title' => __('Edit Profile')]);
    }

    public function updateProfile(ProfileUpdate $request)
    {
        $user = Auth::user();

        if ($request->hasFile('profile_avatar')) {
            Storage::delete($user->profile);
            $user->profile = $request->file('profile_avatar')->store('profileImage');
        }

        $old_email = $user->email;

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->contact_no = $request->contact_no;
        if ($user->save()) {
            if ($old_email != $user->email) {
                Auth::guard('admin')->logout();
                return redirect()->guest(route('admin.login'));
                die;
            }
        }
        flash(trans('flash_message.update', ['entity' => 'Profile']))->success();
        return redirect()->route('admin.profile-view');
    }

    public function updatePassword(ChangePassword $request)
    {
        // dd($request->all());
        if (Hash::check($request->current_password, Auth::user()->password)) {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            flash(trans('flash_message.password_change'))->success();
            $user->save();
        } else {
            flash(trans('flash_message.password_not_match'))->error();
        }
        return redirect()->route('admin.profile-view');
    }

    public function quickLink()
    {
        $roles = Role::where('is_display', 'y')->with(['quickLinks'])->get();
        return view('admin.pages.general.quickLink', compact('roles'))->with(['custom_title' => __('Quick Link')]);
    }

    public function updateQuickLink(Request $request)
    {
        $user = Auth::user();
        if($request->has('roles')){
            //getting all requested roles id of quickLinks
            $requestedRoleIds = array_keys($request->roles);
            //getting admins all roles id of quickLinks
            $quickLinksRoleIds =  $user->quickLinks->pluck('role_id')->toArray();
            //getting diffQuickLinks data
            $diffQuickLinks = array_diff($quickLinksRoleIds,$requestedRoleIds);
            //getting role id values of diffQuickLinks
            $values = array_values($diffQuickLinks);
            if(count($values) > 0) {
                QuickLink::where('admin_id',$user->id)->whereIn('role_id',$values)->delete();
            }
            foreach ($request->roles as $key => $role) {
                if (!empty($role['permissions'])) {
                    $quickLink = QuickLink::updateOrCreate(
                        ['role_id' => $key, 'admin_id' => $user->id],
                        ['link_type' => implode(',', $role['permissions'])]
                    );
                }
            }
        }else{
            //quick links request empty then delete all quick links
            $user->quickLinks()->delete();
        }
        return redirect()->back();
    }

    public function showSetting()
    {
        $settings = Setting::where('editable', '=', 'y')->get();
        return view('admin.pages.general.settings', compact('settings'))->with(['custom_title' => 'Site Setting']);
    }

    public function changeSetting(Request $request)
    {
        $array = array();
        $flag = false;
        $cb_key = $cb_secret = '';
        foreach ($request->input() as $key => $value) {
            $setting = Setting::find($key);
            if ($setting) {
                if ($value != "" || $setting->required == "n") {
                    $setting->value = $value;
                    $setting->save();
                    $array[$setting->constant] = $value;

                    //Config::set('settings.' . $setting->constant, $setting->value);
                }
            }
        }

        //Image Uploading
        foreach ($request->file() as $key => $value) {
            $setting = Setting::find($key);
            if ($request->hasFile($key)) {
                $file_pre = ($setting->constant == 'site_logo') ? 'logo.' : time() . '.';
                $filename = $file_pre . $request->file($key)->getClientOriginalExtension();
                $request->file($key)->move(public_path('frontend/images'), $filename);
                $setting->value = 'frontend/images/' . $filename;
                $setting->save();
                $array[$setting->constant] = 'frontend/images/' . $filename;
            }
        }

        //Non editable value
        $rem_settings = Setting::where('editable', '=', 'n')->get();
        foreach ($rem_settings as $key => $single) {
            $array[$single->constant] = $single->value;
        }
        flash(trans('flash_message.update', ['entity' => 'Settings']))->success();
        return redirect()->route('admin.settings.index');
    }

    public function age_check($dateOfBirth){
        // $dateOfBirth = "30-06-1996";
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        // echo 'Your age is '.$diff->format('%y');
        return $diff->format('%y');
    }

    // public function gender_listing(Request $request)
    // {

    //     $records = [];
    //     extract($this->DTFilters($request->all()));

    //     // count only no of recoad
    //     $city_lists_count = Location::with(['locationTranslation']);
    //     $city_lists_count = $city_lists_count->select("users.*","locations.*","users.location_id as location_id","users.id as user_id");
    //     $city_lists_count = $city_lists_count->join("users","users.location_id","=","locations.id");
    //     $city_lists_count = $city_lists_count->where("users.deleted_at","=",NULL);
    //     $city_lists_count = $city_lists_count->where("users.location_id","!=",NULL);
    //     $city_lists_count = $city_lists_count->groupBy("users.location_id");

    //     if ($search != '') {
    //         $city_lists_count->where(function ($query) use ($search) {
    //             $query->orWhereHas('locationTranslation', function ($q) use ($search) {
    //                 $q->where('name', 'like', "%{$search}%");
    //             });
    //         });
    //     }
    //     $city_lists_count = $city_lists_count->get();


    //     $city_lists = Location::with(['locationTranslation']);
    //     $city_lists = $city_lists->select("users.*","locations.*","users.location_id as location_id","users.id as user_id");
    //     $city_lists = $city_lists->join("users","users.location_id","=","locations.id");
    //     $city_lists = $city_lists->where("users.deleted_at","=",NULL);
    //     $city_lists = $city_lists->where("users.location_id","!=",NULL);
    //     $city_lists = $city_lists->groupBy("users.location_id");

    //     if ($search != '') {
    //         $city_lists->where(function ($query) use ($search) {
    //             $query->orWhereHas('locationTranslation', function ($q) use ($search) {
    //                 $q->where('name', 'like', "%{$search}%");
    //             });
    //         });
    //     }

    //     $city_lists = $city_lists->offset($offset)->limit($limit);
    //     $city_lists = $city_lists->get();

    //     $records['recordsTotal'] = count($city_lists_count);
    //     $records['recordsFiltered'] = count($city_lists_count);
    //     $records['data'] = [];

    //     foreach ($city_lists as $val) {
    //         $all_users          = User::where('location_id','=',$val->location_id)->count();
    //         $total_male_user    = User::where('location_id','=',$val->location_id)
    //                             ->where('gender','=','Male')
    //                             ->count();
    //         $total_female_user  = User::where('location_id','=',$val->location_id)
    //                             ->where('gender','=','Female')
    //                             ->count();
    //         $total_na_user      = User::where('location_id','=',$val->location_id)
    //                             ->whereNull('gender')
    //                                 ->count();
            

    //         $male_pr = $total_male_user/$all_users * 100;
    //         $female_pr = $total_female_user/$all_users * 100;
    //         $na_pr = $total_na_user/$all_users * 100;
    //         $total_users = $total_male_user + $total_female_user + $total_na_user;
    //         $records['data'][] = [
    //             'city_name' => $val->name,
    //             'total_male_pr' => number_format($male_pr,2)."%",
    //             'total_female_pr' => number_format($female_pr,2)."%",
    //             'total_na_pr' => number_format($na_pr,2)."%",
    //         ];
    //     }
    //     $keys = array_column($records['data'], 'total_female_pr');
    //     array_multisort($keys, SORT_DESC, $records['data']);
    //     return $records;
    //     return $records;
    // }

    public function location_listing(Request $request)
    {
        extract($this->DTFilters($request->all()));

        //count only no of recoad
        $city_lists_count = Location::with(['locationTranslation']);
        $city_lists_count = $city_lists_count->select("locations.*","users.location_id as location_id","users.id as user_id",DB::raw("count(users.id) as total_no_of_users"));
        $city_lists_count = $city_lists_count->join("users","users.location_id","=","locations.id");
        $city_lists_count = $city_lists_count->where("users.deleted_at","=",NULL);
        $city_lists_count = $city_lists_count->where("users.location_id","!=",NULL);
        $city_lists_count = $city_lists_count->where("locations.is_active","=",'y');
        $city_lists_count = $city_lists_count->groupBy("users.location_id");

        if ($search != '') {
            $city_lists_count->where(function ($query) use ($search) {
                $query->orWhereHas('locationTranslation', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }
        $city_lists_count = $city_lists_count->orderBy("total_no_of_users","DESC");
        $city_lists_count = $city_lists_count->limit(10);
        $city_lists_count = $city_lists_count->get();


        $city_lists = Location::with(['locationTranslation']);
        $city_lists = $city_lists->select("locations.*","users.location_id as location_id","users.id as user_id",DB::raw("count(users.id) as total_no_of_users"));
        $city_lists = $city_lists->join("users","users.location_id","=","locations.id");
        $city_lists = $city_lists->where("users.deleted_at","=",NULL);
        $city_lists = $city_lists->where("users.location_id","!=",NULL);
        $city_lists = $city_lists->where("locations.is_active","=",'y');
        $city_lists = $city_lists->groupBy("users.location_id");

        if ($search != '') {
            $city_lists->where(function ($query) use ($search) {
                $query->orWhereHas('locationTranslation', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $count = $city_lists->count();
        $records = [];

        $city_lists = $city_lists->offset($offset)->limit($limit);
        $city_lists = $city_lists->orderBy("total_no_of_users","DESC");
        $city_lists = $city_lists->limit(10);
        $city_lists = $city_lists->get();
        $records['recordsTotal'] = count($city_lists_count);
        $records['recordsFiltered'] = count($city_lists_count);
        $records['data'] = [];
        foreach ($city_lists as $val) {
                $all_users          = User::count();
                $total_male_user    = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Male')
                                    ->count();
                $total_female_user  = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Female')
                                    ->count();
                $total_na_user      = User::where('location_id','=',$val->id)
                                    ->whereNull('gender')
                                    ->count();
                                    
                $total_users = $total_male_user + $total_female_user + $total_na_user;
                $pr = $total_users/$all_users * 100;
                if($total_users > 0){
                    $records['data'][] = [
                        'city_name' => $val->name,
                        'state_name' => $val->state,
                        'total_male_user' => $total_male_user,
                        'total_female_user' => $total_female_user,
                        'total_na_user' => $total_na_user,
                        'total_users' => $total_users,
                        'pr' => number_format($pr,2)."%",
                    ];
                }
        }
        $keys = array_column($records['data'], 'total_users');
        array_multisort($keys, SORT_DESC, $records['data']);
        return $records;
    }

    // language list
    public function language_listing(Request $request)
    {
        extract($this->DTFilters($request->all()));

        $language_lists = Language::select("languages.*",DB::raw("count(users.id) as total_users"));
        $language_lists = $language_lists->join("users","users.language_id","=","languages.id");

        if ($search != '') {
            $language_lists = $language_lists->Where('languages.language', 'like', "%{$search}%");
            $language_lists = $language_lists->orWhere('languages.hint', 'like', "%{$search}%");
        }

        $language_lists = $language_lists->whereNull("users.deleted_at");
        $language_lists = $language_lists->groupBy("languages.id");
        $language_lists = $language_lists->orderBy("total_users","DESC");
        $language_lists = $language_lists->get();
        
        $records = [];
        $records['recordsTotal'] = count($language_lists);
        $records['recordsFiltered'] = count($language_lists);
        $records['data'] = [];

        $dashboard_data = DB::table("analytic_dashboard")->orderBy("id","DESC")->first();
        $all_users = $dashboard_data ? $dashboard_data->total_users : 0;

        foreach ($language_lists as $val) {
                $total_users = $val->total_users;
                $pr = $total_users/$all_users * 100;
                $records['data'][] = [
                    'language' => $val->language,
                    'hint' => $val->hint,
                    'total_pr' => number_format($pr,2)."%",
                    'total_users' => number_format($total_users),
                ];
        }
        // $keys = array_column($records['data'], 'total_users');
        // array_multisort($keys, SORT_DESC, $records['data']);
        return $records;
    }

    // delete not used location
    // public function deletelocation()
    // {
    //      $locationlist = DB::table('locations')
    //         ->select("locations.id")
    //         ->leftJoin('users', function($join) {
    //             $join->on('locations.id', '=', 'users.location_id');
    //         })
    //         ->whereNull('users.location_id')
    //         ->get();
    //         // ->paginate(5);
    //     // echo "<pre>"; 
    //     // print_r($locationlist);
    //     // die();
    //     foreach ($locationlist as $key => $val) {
    //         Location::where('id',$val->id)->delete();
    //         LocationTranslation::where('location_id',$val->id)->delete();
    //     }
    //     echo "done";

    //     die();
    // }
}
