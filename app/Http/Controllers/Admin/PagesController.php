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
use App\Models\State;
use App\Models\SubscriptionPlanTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use DB;
class PagesController extends Controller
{

    public function dashboard()
    {   
        // echo Hash::make(12345678); die();
        $location_result = array();
        $city_result = array(); 
        $subscription_result = array(); 
        $age_result = array(); 

        $user['Count'] = User::whereNull('deleted_at')->count();
        $user['total_city'] = City::count();
        $user['total_male'] = User::where('gender','=','Male')->whereNull('deleted_at')->count();
        $user['total_female'] = User::where('gender','=','Female')->whereNull('deleted_at')->count();
        $user['total_na_user'] = User::whereNull('gender')->whereNull('deleted_at')->count();
        $user['total_subscribed'] = User::where('gender','=','Male')->where('is_subscribed','=','y')->whereNull('deleted_at')->count();
        $user['total_unsubscribed'] = User::where('gender','=','Male')->where('is_subscribed','!=','y')->whereNull('deleted_at')->count();
        $subscription_plans = SubscriptionPlanTranslation::where(['locale' => 'en'])->get();
        // echo "<pre>"; print_r($subscription_plans->toArray()); die();
        $no_of_sub_buy = 0;
        if (count($subscription_plans) > 0) {
            foreach ($subscription_plans as $key => $val) {
                $total_users        = User::with('userTransDefault','subscription.subscriptionPlan')->get();
                if (count($total_users) > 0) {
                    foreach ($total_users as $key => $row) {
                        if($row->is_subscribed == 'y')
                        {   
                            $user_active_plan_id = isset($row->subscription->plan_id) ?  $row->subscription->plan_id : 0;
                            if ($user_active_plan_id == $val->subscription_plan_id) {
                                $no_of_sub_buy += 1;
                            }
                        }
                    }
                }
                $subscription_result[] = [
                    'name' => $val->name,
                    'total_users' => $no_of_sub_buy,
                ];
                $no_of_sub_buy = 0;
            }
        }
        // echo "<pre>"; print_r($subscription_result); die();
        // die();
        $city_list = City::with(['state.stateTransDefault','cityTransDefault'])->get();
        if(count($city_list) > 0){
            foreach ($city_list as $key => $val) {
                $all_users              = User::count();
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
                    $location_result[] = [
                        'city_name' => $val->name,
                        'state_name' =>  $val->state ? $val->state->stateTransDefault ? $val->state->stateTransDefault->name : "" : "",
                        'total_male_user' => $total_male_user,
                        'total_female_user' => $total_female_user,
                        'total_na_user' => $total_na_user,
                        'total_users' => $total_users,
                        'pr' => number_format($pr,2),
                    ];
                }
            }
        }

        $city_lists = City::get();
        if(count($city_lists) > 0){
            foreach ($city_lists as $key => $val) {
                $total_users        = User::where('location_id','=',$val->id)
                                    ->whereNull('deleted_at')
                                    ->count();
                $all_users          = User::whereNull('deleted_at')->count();
                $total_male_user    = User::where('location_id','=',$val->id)
                                        ->where('gender','=','Male')
                                        ->whereNull('deleted_at')
                                        ->count();
                $total_female_user  = User::where('location_id','=',$val->id)
                                        ->where('gender','=','Female')
                                        ->whereNull('deleted_at')
                                        ->count();
                $total_na_user      = User::where('location_id','=',$val->id)
                                        ->whereNull('gender')
                                        ->whereNull('deleted_at')
                                        ->count();

                $male_pr = $total_male_user/$all_users * 100;
                $female_pr = $total_female_user/$all_users * 100;
                $na_pr = $total_na_user/$all_users * 100;
                $total_users = $total_male_user + $total_female_user + $total_na_user;
                if($total_users > 0){
                    $city_result[] = [
                        'city_name' => $val->name,
                        'total_male_pr' => number_format($male_pr,2),
                        'total_female_pr' => number_format($female_pr,2),
                        'total_na_pr' => number_format($na_pr,2),
                    ];
                }
            }
        }

        $all_users = User::select('birth_date','gender')->whereNull('deleted_at')->whereNotNull('birth_date')->whereNotNull('gender')->get();
        // echo "<pre>"; print_r($all_users->toArray()); die();
        $male_age_18_25   = 0;
        $male_age_26_35   = 0;
        $male_age_36_45   = 0;
        $male_age_45      = 0;
        $female_age_18_25 = 0;
        $female_age_26_35 = 0;
        $female_age_36_45 = 0;
        $female_age_45    = 0;
        if(count($all_users) > 0){
            foreach ($all_users as $key => $val) {
                $age_check = $this->age_check($val->birth_date);
                // echo $age_check; echo "<br>";
                if ($val->gender == "Male") {
                    if ($age_check >= 18 && $age_check <= 25) {
                        $male_age_18_25 += 1;
                    }
                    if ($age_check >= 26 && $age_check <= 35) {
                        $male_age_26_35 += 1;
                    }
                    if ($age_check >= 36 && $age_check <= 45) {
                        $male_age_36_45 += 1;
                    }
                    if ($age_check >= 46) {
                        $male_age_45 += 1;
                    }
                }

                if ($val->gender == "Female") {
                    if ($age_check >= 18 && $age_check <= 25) {
                        $female_age_18_25 += 1;
                    }
                    if ($age_check >= 26 && $age_check <= 35) {
                        $female_age_26_35 += 1;
                    }
                    if ($age_check >= 36 && $age_check <= 45) {
                        $female_age_36_45 += 1;
                    }
                    if ($age_check >= 46) {
                        $female_age_45 += 1;
                    }
                }
            }
            
            $age_result[] = [
                'male_age_18_25'    => $male_age_18_25,
                'male_age_26_35'    => $male_age_26_35,
                'male_age_36_45'    => $male_age_36_45,
                'male_age_45'       => $male_age_45,
                'female_age_18_25'  => $female_age_18_25,
                'female_age_26_35'  => $female_age_26_35,
                'female_age_36_45'  => $female_age_36_45,
                'female_age_45'     => $female_age_45,
            ];
        }

        $user['location_result'] = [];
        $user['city_result'] = [];
        $user['subscription_result'] = $subscription_result;
        $user['age_result'] = $age_result;
        // echo "<pre>"; print_r($location_result); die();
        cache()->forget('oldest-record'); //forget cache recorde change on development
        $old_date = cache()->rememberForever('oldest-record', function () {
            return User::selectRaw('created_at')->orderBy('created_at', 'asc')->first();
        });

        if (isset($old_date->created_at)) {
            $startDate = Carbon::parse($old_date->created_at)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $diffInDays = $startDate->diffInDays($endDate);

            //diffInDays same date return 0 day and if date 4 and 5 diffInDays return 1 day
            $diffInDays = $diffInDays + 1;
            $user['PerDayCount'] = $diffInDays >= 1 ? number_format(floor(($user['Count'] / $diffInDays))) : 0; //Per Day Register User
            $user['PerWeekCount'] = $diffInDays >= 7 ? number_format(floor(($user['Count'] / ($diffInDays / 7)))) : 0; //Per Week Register User
            $user['Per30DayCount'] = $diffInDays >= 30 ? number_format(floor(($user['Count'] / ($diffInDays / 30)))) : 0; //Per 30 Day Register User
            $user['Count'] = number_format($user['Count']);
        }
        return view('admin.pages.general.dashboard', compact('user'))->with(['custom_title' => __('Dashboard')]);
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

    public function gender_listing(Request $request)
    {

        $records = [];
        extract($this->DTFilters($request->all()));

        // count only no of recoad
        $city_lists_count = Location::with(['locationTranslation']);
        $city_lists_count = $city_lists_count->select("users.*","locations.*","users.location_id as location_id","users.id as user_id");
        $city_lists_count = $city_lists_count->join("users","users.location_id","=","locations.id");
        $city_lists_count = $city_lists_count->where("users.deleted_at","=",NULL);
        $city_lists_count = $city_lists_count->where("users.location_id","!=",NULL);
        $city_lists_count = $city_lists_count->groupBy("users.location_id");

        if ($search != '') {
            $city_lists_count->where(function ($query) use ($search) {
                $query->orWhereHas('locationTranslation', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }
        $city_lists_count = $city_lists_count->get();


        $city_lists = Location::with(['locationTranslation']);
        $city_lists = $city_lists->select("users.*","locations.*","users.location_id as location_id","users.id as user_id");
        $city_lists = $city_lists->join("users","users.location_id","=","locations.id");
        $city_lists = $city_lists->where("users.deleted_at","=",NULL);
        $city_lists = $city_lists->where("users.location_id","!=",NULL);
        $city_lists = $city_lists->groupBy("users.location_id");

        if ($search != '') {
            $city_lists->where(function ($query) use ($search) {
                $query->orWhereHas('locationTranslation', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $city_lists = $city_lists->offset($offset)->limit($limit);
        $city_lists = $city_lists->get();

        $records['recordsTotal'] = count($city_lists_count);
        $records['recordsFiltered'] = count($city_lists_count);
        $records['data'] = [];

        foreach ($city_lists as $val) {
            $all_users          = User::where('location_id','=',$val->location_id)->count();
            $total_male_user    = User::where('location_id','=',$val->location_id)
                                ->where('gender','=','Male')
                                ->count();
            $total_female_user  = User::where('location_id','=',$val->location_id)
                                ->where('gender','=','Female')
                                ->count();
            $total_na_user      = User::where('location_id','=',$val->location_id)
                                ->whereNull('gender')
                                    ->count();
            

            $male_pr = $total_male_user/$all_users * 100;
            $female_pr = $total_female_user/$all_users * 100;
            $na_pr = $total_na_user/$all_users * 100;
            $total_users = $total_male_user + $total_female_user + $total_na_user;
            $records['data'][] = [
                'city_name' => $val->name,
                'total_male_pr' => number_format($male_pr,2)."%",
                'total_female_pr' => number_format($female_pr,2)."%",
                'total_na_pr' => number_format($na_pr,2)."%",
            ];
        }
        $keys = array_column($records['data'], 'total_female_pr');
        array_multisort($keys, SORT_DESC, $records['data']);
        return $records;
        return $records;
    }

    public function location_listing(Request $request)
    {
        extract($this->DTFilters($request->all()));

        // count only no of recoad
        $city_lists_count = Location::with(['locationTranslation']);
        $city_lists_count = $city_lists_count->select("users.*","locations.*","users.location_id as location_id","users.id as user_id");
        $city_lists_count = $city_lists_count->leftJoin("users","users.location_id","=","locations.id");
        $city_lists_count = $city_lists_count->where("users.deleted_at","=",NULL);
        $city_lists_count = $city_lists_count->where("users.location_id","!=",NULL);
        $city_lists_count = $city_lists_count->where("locations.is_active","=",'y');
        // $city_lists_count = $city_lists_count->orderBy("users.location_id","ASC");
        $city_lists_count = $city_lists_count->groupBy("users.location_id");

        if ($search != '') {
            $city_lists_count->where(function ($query) use ($search) {
                $query->orWhereHas('locationTranslation', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $city_lists_count = $city_lists_count->get();


        $city_lists = Location::with(['locationTranslation']);
        $city_lists = $city_lists->select("users.*","locations.*","users.location_id as location_id","users.id as user_id");
        $city_lists = $city_lists->leftJoin("users","users.location_id","=","locations.id");
        $city_lists = $city_lists->where("users.deleted_at","=",NULL);
        $city_lists = $city_lists->where("users.location_id","!=",NULL);
        $city_lists = $city_lists->where("locations.is_active","=",'y');
        // $city_lists = $city_lists->orderBy("users.location_id","ASC");
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
}
