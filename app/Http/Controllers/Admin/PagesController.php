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
use App\Models\State;
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
        $location_result = array();
        $city_result = array(); 

        $user['Count'] = User::whereNull('deleted_at')->count();
        $user['total_city'] = City::count();
        $user['total_male'] = User::where('gender','=','Male')->whereNull('deleted_at')->count();
        $user['total_female'] = User::where('gender','=','Female')->whereNull('deleted_at')->count();
        $user['total_subscribed'] = User::where('gender','=','Male')->where('is_subscribed','=','y')->whereNull('deleted_at')->count();
        $user['total_unsubscribed'] = User::where('gender','=','Male')->where('is_subscribed','!=','y')->whereNull('deleted_at')->count();
        
        $city_list = City::with(['state.stateTransDefault','cityTransDefault'])->get();
        if(count($city_list) > 0){
            foreach ($city_list as $key => $val) {
                $total_users        = User::where('location_id','=',$val->id)
                                    ->count();
                $all_users          = User::count();
                $total_male_user    = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Male')
                                    ->count();
                $total_female_user  = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Female')
                                    ->count();
                $pr = $total_users/$all_users * 100;
                $location_result[] = array(
                    'city_name' => $val->name,
                    'state_name' =>  $val->state ? $val->state->stateTransDefault ? $val->state->stateTransDefault->name : "" : "",
                    'total_male_user' => $total_male_user,
                    'total_female_user' => $total_female_user,
                    'total_users' => $total_users,
                    'pr' => number_format($pr,2),
                );
            }
        }

        $city_lists = City::get();
        if(count($city_lists) > 0){
            foreach ($city_lists as $key => $val) {
                $total_users        = User::where('location_id','=',$val->id)
                                    ->count();
                $all_users          = User::count();
                $total_male_user    = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Male')
                                    ->count();
                $total_female_user  = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Female')
                                    ->count();
                $male_pr = $total_male_user/$all_users * 100;
                $female_pr = $total_female_user/$all_users * 100;
                $city_result[] = array(
                    'city_name' => $val->name,
                    'total_male_pr' => number_format($male_pr,2),
                    'total_female_pr' => number_format($female_pr,2),
                );
            }
        }
        $user['location_result'] = $location_result;
        $user['city_result'] = $city_result;
        // echo "<pre>"; print_r($city_result); die();
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
            $user['PerDayCount'] = $diffInDays >= 1 ? number_format_short(floor(($user['Count'] / $diffInDays))) : 0; //Per Day Register User
            $user['PerWeekCount'] = $diffInDays >= 7 ? number_format_short(floor(($user['Count'] / ($diffInDays / 7)))) : 0; //Per Week Register User
            $user['Per30DayCount'] = $diffInDays >= 30 ? number_format_short(floor(($user['Count'] / ($diffInDays / 30)))) : 0; //Per 30 Day Register User
            $user['Count'] = number_format_short($user['Count']);
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

    public function gender_pr_listing(Request $request)
    {
        $results = array();
        extract($this->DTFilters($request->all()));
        $city_lists = City::get($sort_column, $sort_order);
        if(count($city_lists) > 0){
            foreach ($city_lists as $key => $val) {
                $total_users        = User::where('location_id','=',$val->id)
                                    ->count();
                $all_users          = User::count();
                $total_male_user    = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Male')
                                    ->count();
                $total_female_user  = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Female')
                                    ->count();
                $male_pr = $total_male_user/$all_users * 100;
                $female_pr = $total_female_user/$all_users * 100;
                $results[] = array(
                    'id' => $val->id,
                    'city_name' => $val->name,
                    'total_male_pr' => number_format($male_pr,2),
                    'total_female_pr' => number_format($female_pr,2),
                );
            }
        }
        $count = $city_lists->count();
        $records = [];
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = $results;
        return response()->json($records);
    }

    public function location_pr_listing(Request $request)
    {
        $location_result = array();
        extract($this->DTFilters($request->all()));
        $city_list = City::with(['state.stateTransDefault','cityTransDefault'])->get();
        if(count($city_list) > 0){
            foreach ($city_list as $key => $val) {
                $total_users        = User::where('location_id','=',$val->id)
                                    ->count();
                $all_users          = User::count();
                $total_male_user    = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Male')
                                    ->count();
                $total_female_user  = User::where('location_id','=',$val->id)
                                    ->where('gender','=','Female')
                                    ->count();
                $pr = $total_users/$all_users * 100;
                $location_result[] = array(
                    'id' => $val->id,
                    'city_name' => $val->name,
                    'state_name' =>  $val->state ? $val->state->stateTransDefault ? $val->state->stateTransDefault->name : "" : "",
                    'total_male_user' => $total_male_user,
                    'total_female_user' => $total_female_user,
                    'total_users' => $total_users,
                    'pr' => number_format($pr,2),
                );
            }
        }
        $count = $city_list->count();
        $records = [];
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = $location_result;
        return response()->json($records);
    }
}
