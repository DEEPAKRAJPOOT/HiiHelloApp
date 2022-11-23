<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\Personality;
use App\Models\ProfileDetail;
use App\Models\Interest;
use App\Models\UserInterest;
use App\Models\UserPersonality;
use App\Models\Language;
use App\Models\Location;
use App\Models\Country;
use App\Models\SubscriptionPlanTranslation;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\NotificationJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.users.index')->with(['custom_title' => 'Users']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $personalities = Personality::with('personalityTransDefault')->where('is_active', 'y')->get();
        $attributes = ProfileDetail::with(['profileDetailTransDefault'])->where(['is_active' => 'y'])->get();
        $interests = Interest::with(['subInterests.interestTransDefault', 'subInterests.subInterests.interestTransDefault'])->where(['is_active' => 'y'])->get();
        $countries = Country::where(['is_active' => 'y'])->get();
        $locations = Location::with(['locationTransDefault'])->where(['is_active' => 'y'])->get();
        $languages = Language::where(['is_active' => 'y'])->get();
        return view('admin.pages.users.create', compact('personalities', 'interests', 'countries', 'attributes', 'locations', 'languages'))->with(['custom_title' => 'User']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();
            $path = NULL;
            $traslate_data = [];
            if ($request->has('profile_photo')) {
                $path = $request->file('profile_photo')->store('users/profile_photo');
            }

            $user = User::create($request->all());
            $user['custom_id']   =  getUniqueString('users');
            $user['password']    =  Hash::make(config('utility.default_password'));
            $user->profile_photo =  $path;

            if (!empty($request->country_code)) {
                $country = Country::wherePhonecode($request->country_code)->whereIsActive('y')->firstOrFail();
                $user->country_id = $country->id;
            }
            if (!empty($request->location)) {
                $location = Location::whereId($request->location)->whereIsActive('y')->firstOrFail();
                $user->location_id = $location->id;
                $user->discover_location_id = $location->id;
            }
            if (!empty($request->language)) {
                $language = Language::whereLangCode($request->language)->whereIsActive('y')->firstOrFail();
                $user->language_id = $language->id;
            }

            // Store Account Id
            if (!empty($request->language) && $request->language == 'en') {
                $user->account_id = Str::slug(substr($request->full_name, 0, 4), "_") . '_' . time();
            }

            // Full name
            $language_codes = Language::pluck('lang_code')->toArray();
            if (!empty($request->full_name)) {
                foreach ($language_codes as $language_code) {
                    $traslate_data[$language_code] =  ['full_name' =>  $request->full_name];
                }
                $user->update($traslate_data);
                $user->is_trans_full_name = 'n';
            }

            if (!empty($request->fav_movie)) {
                foreach ($language_codes as $language_code) {
                    $traslate_data[$language_code] =  ['fav_movie' =>  $request->fav_movie];
                }
                $user->update($traslate_data);
                $user->is_trans_fav_movie = 'n';
            }

            if (!empty($request->about_me)) {
                foreach ($language_codes as $language_code) {
                    $traslate_data[$language_code] =  ['about_me' =>  $request->about_me];
                }
                $user->update($traslate_data);
                $user->is_trans_about_me = 'n';
            }

            // Set Contact Number As Verified
            if (!empty($user->contact_no)) {
                $user->contact_verified_at = \Carbon\Carbon::now();
            }

            /* Verification Details */
            $verify_photo   =    $request->verify_photo;
            $verify_video   =    $request->verify_video;

            if ($verify_photo == 'y' && empty($user->photo_verified_at)) {
                $user->photo_verified_at = \Carbon\Carbon::now();
            } elseif ($verify_photo == NULL) {
                $user->photo_verified_at = NULL;
            }

            if ($verify_video == 'y' && empty($user->video_verified_at)) {
                $user->video_verified_at = \Carbon\Carbon::now();
            } elseif ($verify_video == NULL) {
                $user->video_verified_at = NULL;
            }

            // Set Default Discover
            $user->discover_distance    = config('utility.profile.detail.discover_distance');
            $user->discover_start_age   = config('utility.profile.detail.discover_start_age');
            $user->discover_end_age     = config('utility.profile.detail.discover_end_age');

            /* User Personality */
            if (!empty($request->personalities)) {
                foreach ($request->personalities as $personality_id) {
                    UserPersonality::updateOrCreate([
                        'user_id'         => $user->id,
                        'personality_id'  => $personality_id,
                    ], [
                        'custom_id'     => getUniqueString('user_personalities'),
                    ]);
                }
            }

            /* User Interest */
            if (!empty($request->traveling_id)) {
                foreach ($request->traveling_id as $traveling) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $traveling,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->music_id)) {
                foreach ($request->music_id as $music) {
                    UserInterest::updateOrCreate([
                        'user_id' => $user->id,
                        'interest_id' => $music,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->hobbie_id)) {
                foreach ($request->hobbie_id as $hobby) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $hobby,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->game_id)) {
                foreach ($request->game_id as $game) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $game,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->sport_id)) {
                foreach ($request->sport_id as $sport) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $sport,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->food_id)) {
                foreach ($request->food_id as $food) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $food,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->actor_id)) {
                foreach ($request->actor_id as $actor) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $actor,
                    ], [

                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if (!empty($request->singer_id)) {
                foreach ($request->singer_id as $singer) {
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $singer,
                    ], [
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }

            $user->profile_percentage = $user->calculateProfilePercent();

            // Buy Subscription For Girls
            if($user->wasRecentlyCreated){
                $user->buyFreeSubscription();
            }

            if ($user->save()) {
                DB::commit();
                flash('User account created successfully!')->success();
            } else {
                flash('Unable to save avatar. Please try again later.')->error();
            }
            return redirect(route('admin.users.index'));
        } catch (QueryException $e) {
            DB::rollback();
            return redirect()->back()->flash('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::with([
            'userTranslation', 'userTransDefault', 'userDetails',
            'country.countryTransDefault', 'location.locationTransDefault', 'language',
            'interests.interest.interestTransDefault', 'religion.profileDetailTransDefault',
            'relationshipStatus.profileDetailTransDefault', 'youAreHere.profileDetailTransDefault',
            'foodPreference.profileDetailTransDefault', 'drinking.profileDetailTransDefault',
            'smoking.profileDetailTransDefault', 'starSign.profileDetailTransDefault',
            'religion.profileDetailTransDefault', 'community.profileDetailTransDefault',
            'education.profileDetailTransDefault',
            'university.profileDetailTransDefault', 'profession.profileDetailTransDefault',
            'personalities.personality.personalityTransDefault'
        ])->whereId($user->id)->firstOrFail();
        return view('admin.pages.users.view', compact('user'))->with(['custom_title' => 'User']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = User::with('userTransDefault','subscription.subscriptionPlan')->whereId($user->id)->firstOrFail();
        $personalities = Personality::with('personalityTransDefault')->where('is_active', 'y')->get();
        $attributes = ProfileDetail::with(['profileDetailTransDefault'])->where(['is_active' => 'y'])->get();
        $interests = Interest::with(['subInterests.interestTransDefault', 'subInterests.subInterests.interestTransDefault'])
            ->where(['is_active' => 'y'])->get();
        $countries = Country::where(['is_active' => 'y'])->get();
        $locations = Location::with(['locationTransDefault'])->where(['is_active' => 'y'])->get();
        $languages = Language::where(['is_active' => 'y'])->get();


        //CHNAGE USER SUBCRIPTION PLAN 14:SEP START

        $subscription_plans = SubscriptionPlanTranslation::where(['locale' => 'en'])->get();
        //is_subscribed
        //subscription_end_date
        //dd($user->subscription->plan_id);
        //echo "<br> User Is Subscribe :".$user->is_subscribed;
        //echo "<br> User Is Subscribe End Date:".$user->subscription_end_date;
        $user_active_plan_id = 0;
        $plan_paid_from = "";
        if($user->is_subscribed=='y')
        {   
            $user_active_plan_id = isset($user->subscription->plan_id) ?  $user->subscription->plan_id : 0;
            $plan_paid_from = $user->subscription->payment_type;
        }
        //CHNAGE USER SUBCRIPTION PLAN 14:SEP END    

        //user interest
        $user_interest = UserInterest::where('user_id', $user->id)->pluck('interest_id')->toArray();
        $user_personality = UserPersonality::where('user_id', $user->id)->pluck('personality_id')->toArray();
        return view('admin.pages.users.edit', compact('user', 'user_personality', 'personalities', 'user_interest', 'interests', 'attributes', 'countries', 'locations', 'languages','subscription_plans','user_active_plan_id','plan_paid_from'))->with(['custom_title' => 'Users']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {

        try {
            DB::beginTransaction();
            if (!empty($request->action) && $request->action == 'change_status') {
                $content = ['status' => 204, 'message' => "something went wrong"];
                if ($user) {
                    $user->is_active = $request->value;
                    if ($user->save()) {
                        DB::commit();
                        $content['status'] = 200;
                        $content['message'] = "Status updated successfully.";
                    }
                }
                return response()->json($content);
            } else {

                //UPDATE SUBSCRIPTION FOR USER START
                if(isset($request->subcription_plan) && ($request->user_active_plan != $request->subcription_plan))
                {

                    if(intval($request->subcription_plan)==0)
                    {                                 
                            $user->is_subscribed='n';
                            $user->subscription_end_date= NULL;                             
                    }
                    else
                    {
                     
                            $plan = SubscriptionPlan::whereId($request->subcription_plan)->whereIsActive('y')->firstOrFail();                   

                            $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                            $subscription_end_date = strtotime("+".$plan->months." months", strtotime($new_subscription_start_date)); // returns timestamp
                            $subscription_end_date = date('Y-m-d',$subscription_end_date); // formatted version
                            
                            $subscription =  Subscription::create([
                                'custom_id'                 =>  getUniqueString('subscriptions'),
                                'user_id'                   =>  $user->id ?? NULL,
                                'plan_id'                   =>  $plan->id ?? NULL,
                                'email'                     =>  $user->email,
                                'months'                    =>  $plan->months,
                                'amount'                    =>  0,
                                'start_date'                =>  $new_subscription_start_date,
                                'end_date'                  =>  $subscription_end_date,
                                'payment_date'              =>  now(),
                                'payment_type'              =>  'admin',
                                'receipt_data'              =>  "",
                                'status'                    =>  "active",
                            ]);

                            $user->is_subscribed='y';
                            $user->subscription_end_date=$subscription_end_date;                    
                     }       
                }
                //UPDATE SUBSCRIPTION FOR USER END

                $verify_notify = $verify_photo_notify = $verify_video_notify = false;
                if ($user->verify_status == 'under_review') {
                    $verify_notify = true;
                }
                if ($user->verify_photo_status != 'unverified') {
                    $verify_photo_notify = true;
                }
                if ($user->verify_video_status != 'unverified') {
                    $verify_video_notify = true;
                }

                $path = $user->profile_photo;
                $not_to_delete_interest = $not_to_delete_personality = array();

                //request has remove_profie_photo then delete user image
                if ($request->has('remove_profie_photo')) {
                    if ($user->profile_photo) {
                        Storage::delete($user->profile_photo);
                    }
                    $path = null;
                }

                if ($request->hasFile('profile_photo')) {
                    if ($user->profile_photo) {
                        Storage::delete($user->profile_photo);
                    }
                    $path = $request->profile_photo->store('users/profile_photo');
                    $user->is_media_checked = 'n';
                }
                $user->fill($request->validated());
                $user->profile_photo = $path;

                if (!empty($request->country_code)) {
                    $country = Country::wherePhonecode($request->country_code)->whereIsActive('y')->firstOrFail();
                    $user->country_id = $country->id;
                }
                if (!empty($request->location)) {
                    $location = Location::whereId($request->location)->whereIsActive('y')->firstOrFail();
                    $user->location_id = $location->id;
                    $user->discover_location_id = $location->id;
                }
                if (!empty($request->language)) {
                    $language = Language::whereLangCode($request->language)->whereIsActive('y')->firstOrFail();
                    $user->language_id = $language->id;
                }

                // Store Account Id
                if (!empty($request->language) && $request->language == 'en') {
                    $user->account_id = Str::slug(substr($request->full_name, 0, 4), "_") . '_' . time();
                }

                // Full name
                $language_codes = Language::pluck('lang_code')->toArray();
                if (!empty($request->full_name)) {
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['full_name' =>  $request->full_name];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_full_name = 'n';
                }

                if (!empty($request->fav_movie)) {
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['fav_movie' =>  $request->fav_movie];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_fav_movie = 'n';
                }

                if (!empty($request->about_me)) {
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['about_me' =>  $request->about_me];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_about_me = 'n';
                }

                /* Verification Details */
                $photo_verified_at  =   $request->photo_verified_at;
                $video_verified_at  =   $request->video_verified_at;

                if ($photo_verified_at == 'y' && empty($user->photo_verified_at)) {
                    $user->photo_verified_at = \Carbon\Carbon::now();
                } elseif ($photo_verified_at == NULL) {
                    $user->photo_verified_at = NULL;
                }

                if ($video_verified_at == 'y' && empty($user->video_verified_at)) {
                    $user->video_verified_at = \Carbon\Carbon::now();
                } elseif ($video_verified_at == NULL) {
                    $user->video_verified_at = NULL;
                }

                /* User Personality */
                if (!empty($request->personalities)) {
                    foreach ($request->personalities as $personality_id) {
                        $custom_id = getUniqueString('user_personalities');
                        UserPersonality::updateOrCreate([
                            'user_id'         => $user->id,
                            'personality_id'  => $personality_id,
                        ], [
                            'custom_id'     => $custom_id,
                        ]);
                        $not_to_delete_personality[] = $custom_id;
                    }
                }
                UserPersonality::where('user_id', $user->id)->whereNotIn('custom_id', $not_to_delete_personality)->delete();

                /* User Interest */
                if (!empty($request->traveling_id)) {
                    foreach ($request->traveling_id as $traveling) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $traveling,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }

                if (!empty($request->music_id)) {
                    foreach ($request->music_id as $music) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id' => $user->id,
                            'interest_id' => $music,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if (!empty($request->hobbie_id)) {
                    foreach ($request->hobbie_id as $hobby) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $hobby,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if (!empty($request->game_id)) {
                    foreach ($request->game_id as $game) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $game,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if (!empty($request->sport_id)) {
                    foreach ($request->sport_id as $sport) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $sport,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if (!empty($request->food_id)) {
                    foreach ($request->food_id as $food) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $food,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if (!empty($request->actor_id)) {
                    foreach ($request->actor_id as $actor) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $actor,
                        ], [

                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }

                if (!empty($request->singer_id)) {
                    foreach ($request->singer_id as $singer) {
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                            'user_id'  => $user->id,
                            'interest_id' => $singer,
                        ], [
                            'custom_id' => $custom_id,
                        ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }

                UserInterest::where('user_id', $user->id)->whereNotIn('custom_id', $not_to_delete_interest)->delete();

                $user->profile_percentage = $user->calculateProfilePercent();

                $finalVerificationStatus = ($user->emailVerifyStatus()=='verified' && $user->contactVerifyStatus()=='verified' && $user->verify_photo_status=='verified')? true : false; 

                if($user->verify_photo_status=="verified" && $finalVerificationStatus==true)
                {
                    $user->verify_status = "verified";
                }


                if ($user->save()) {
                    // Notify Profile Verification
                    if ($verify_notify && $user->verify_status != 'under_review') {
                        if ($user->verify_status == 'verified') {
                            $title = trans('api.notify_message.profile_verified.title');
                            $message = trans('api.notify_message.profile_verified.message');
                            $type = config('utility.notification.type.profile_verified');
                        } else {
                            $title = trans('api.notify_message.profile_not_verified.title');
                            $message = trans('api.notify_message.profile_not_verified.message');
                            $type = config('utility.notification.type.profile_not_verified');
                        }

                        $notification = [
                            'custom_id'     =>  getUniqueString('notifications'),
                            'key'           =>  'user_id',
                            'value'         =>  $user->id,
                            'user_id'       =>  $user->id,
                            'title'         =>  $title,
                            'message'       =>  $message,
                            'image'         =>  '',
                            'type'          =>  $type,
                        ];

                        // Notify
                       /* $notificationJob = new NotificationJob($notification, $user);
                        dispatch($notificationJob);*/
                    }

                    // Notify Photo
                    if ($verify_photo_notify && $user->verify_photo_status == 'unverified') {
                        $notification = [
                            'custom_id'     =>  getUniqueString('notifications'),
                            'key'           =>  'user_id',
                            'value'         =>  $user->id,
                            'user_id'       =>  $user->id,
                            'title'         =>  trans('api.notify_message.verify_fail_photo.title'),
                            'message'       =>  trans('api.notify_message.verify_fail_photo.message'),
                            'image'         =>  '',
                            'type'          =>  config('utility.notification.type.verify_fail_photo'),
                        ];

                        // Notify
                        /*$notificationJob = new NotificationJob($notification, $user);
                        dispatch($notificationJob);*/
                    }

                    // Notify Video
                    if ($verify_video_notify && $user->verify_video_status == 'unverified') {
                        $notification = [
                            'custom_id'     =>  getUniqueString('notifications'),
                            'key'           =>  'user_id',
                            'value'         =>  $user->id,
                            'user_id'       =>  $user->id,
                            'title'         =>  trans('api.notify_message.verify_fail_video.title'),
                            'message'       =>  trans('api.notify_message.verify_fail_video.message'),
                            'image'         =>  '',
                            'type'          =>  config('utility.notification.type.verify_fail_video'),
                        ];

                        // Notify
                        $notificationJob = new NotificationJob($notification, $user);
                        dispatch($notificationJob);
                    }

                    DB::commit();
                    flash('User details updated successfully!')->success();
                } else {
                    flash('Unable to update user. Try again later')->error();
                }
                return redirect(route('admin.users.index'));
            }
        } catch (QueryException $e) {
            DB::rollback();
            return redirect()->back()->flash('error', $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];

            // $users_profile_photos = User::whereIn('custom_id', explode(',', $request->ids))->pluck('profile_photo')->toArray();
            // foreach ($users_profile_photos as $image) {
            //     if(!empty($image)){
            //       Storage::delete($image);
            //     }
            // }
            User::whereIn('custom_id', explode(',', $request->ids))->forceDelete();
            $content['status'] = 200;
            $content['message'] = "User deleted successfully.";
            $content['count'] = User::all()->count();
            return response()->json($content);
        } else {
            $user = User::where('custom_id', $id)->firstOrFail();
            // if( $user->profile_photo ){
            //     Storage::delete($user->profile_photo);
            // }
            $user->forceDelete();
            //$user->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "User deleted successfully.", 'count' => User::all()->count());
                return response()->json($content);
            } else {
                flash('User deleted successfully.')->success();
                return redirect()->route('admin.users.index');
            }
        }
    }

    public function bindDataToQuery($queryItem){
        $query = $queryItem['query'];
        $bindings = $queryItem['bindings'];
        $arr = explode('?',$query);
        $res = '';
        foreach($arr as $idx => $ele){
            if($idx < count($arr) - 1){
                $res = $res.$ele."'".$bindings[$idx]."'";
            }
        }
        $res = $res.$arr[count($arr) -1];
        return $res;
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));

        DB::enableQueryLog();

        $flgPendingProfile = $request->flgPendingProfile;

        $records = [];
        $users = User::with('userTransDefault','location')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('account_id', 'like', "%{$search}%")
                    ->orWhere('profile_percentage', 'like', "%{$search}%")
                    ->orWhere('country_code', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('interest', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('userTransDefault', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        // For Pending Profile Verify
        if($flgPendingProfile > 0) {
            //verify_photo not null
            $users->where('verify_photo_status', '=' , 'under_review')->where('verify_photo', '!=' , '');
        }

        $count = $users->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

       
        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->get();

        // dd(DB::getQueryLog());
        // exit();

        foreach ($users as $user) {

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'getaction' => $user->is_active,
                'class' => '',
                'id' => $user->custom_id,
                'user_id' => $user->id,
                'male_user' => ($user->gender == 'Male' ? 'selected' : ''),
                'female_user' => ($user->gender == 'Female' ? 'selected' : ''),
                'na_user' => ($user->gender == '' ? 'selected' : ''),
            ];

            $records['data'][] = [
                'id' => $user->id,
                'account_id' => $user->account_id ?? "N/A",
                'full_name' =>  $user->userTransDefault ? $user->userTransDefault->full_name : "N/A",
                'profile_percentage' =>  $user->profile_percentage ?? 0,
                'contact_no' => $user->contact_no ? '<a href="tel:' . $user->country_code . '' . $user->contact_no . '" >' . $user->country_code . '' . $user->contact_no . '</a>' : 'N/A',
                'email' => $user->email ? '<a href="mailto:' . $user->email . '" >' . $user->email . '</a>' : 'N/A',
                'gender' => view('admin.layouts.includes.gender', compact('params'))->render(),
                'profile_photo' => view('admin.layouts.includes.photos_verify')->with('profile_photo', $user->profile_photo  ?? 'N/A')->render(),
                'city' => $user->location->name ?? 'N/A',                
                'created_at' => date('Y-m-d H:i:s', strtotime($user->created_at)) ?? 'N/A',
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $user->custom_id], $user)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox', compact('params'))->with('id', $user->custom_id)->render(),
            ];
        }
        // dd($records);
        return $records;
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->get();
        return view('admin.pages.users.trashed', compact('users'))->with(['custom_title' => 'TRASHED']);
    }

    public function trashedData(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $users = User::with('userTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('country_code', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('interest', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $count = $users->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->onlyTrashed()->get();
        foreach ($users as $user) {

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'display' => ($user->is_display == 'y' ? 'checked' : ''),
                'getaction' => $user->is_active,
                'class' => '',
                'id' => $user->id,
            ];

            $records['data'][] = [
                'full_name' =>  $user->userTransDefault ? $city->userTransDefault->full_name : "",
                'country_code' => $user->country_code,
                'contact_no' => $user->contact_no ? '<a href="tel:' . $user->contact_no . '" >' . $user->contact_no . '</a>' : 'N/A',
                'gender' => $user->gender,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'display' => view('admin.layouts.includes.switchDisplay', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $user->id], $user)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $user->id)->render(),
            ];
        }
        return $records;
    }

    public function csvDownload(Request $request)
    {
        $down_file_name = 'User';
        $users = User::with('userTransEn', 'deviceToken', 'country', 'location', 'language','subscription.subscriptionPlan.subscriptionPlanTranslation')
            ->orderBy('created_at','desc')->get();
        if (!$users->isEmpty()) {
            foreach ($users as $user) {
                $data[] = [
                    'Account Id'            =>  $user->account_id ?? "",
                    'Name'                  =>  $user->userTransEn ? $user->userTransEn->full_name ?? "" : "",
                    'Email'                 =>  $user->email ?? "",
                    'Birth Date'            =>  $user->birth_date,
                    'Contact No'            =>  $user->country_code." ".$user->contact_no ?? "",
                    'Verify Video Status'   =>  $user->verify_video_status ?? "",
                    'Verify Photo Status'   =>  $user->verify_photo_status ?? "",
                    'Gender'                =>  $user->gender ?? "",
                    'Location'              =>  $user->location->name ?? "",
                    'Intrest'               =>  $user->interest ?? "",
                    'Verify Status'         =>  $user->verify_status ?? "",
                    'Profile Percentage'    =>  $user->profile_percentage ?? "",
                    'Language'              =>  $user->language ? $user->language->language ?? "" : "",
                    'Langauge Code'         =>  $user->language ? $user->language->lang_code ?? "" : "",
                    'Swipe Count'           =>  $user->swipe_count ?? "",
                    'Like Count'            =>  $user->like_count ?? "",
                    'Match Count'           =>  $user->match_count ?? "",
                    'Chat Count'            =>  $user->chat_count ?? "",
                    'Is Social User'        =>  $user->is_social_user ?? "",
                    'Is Subscribed'         =>  $user->is_subscribed ?? "",
                    'Subscription End Date' =>  $user->subscription_end_date ?? "",
                    'Email Verified Date'   =>  $user->email_verified_at ?? "",
                    'Contact Verified Date' =>  $user->contact_verified_at ?? "",
                    'Photo Verified Date'   =>  $user->photo_verified_at ?? "",
                    'Video Verified Date'   =>  $user->video_verified_at ?? "",
                    'Device Name'           =>  $user->deviceToken ? $user->deviceToken->device_name ?? "" : "",
                    'Device Type'           =>  $user->deviceToken ? $user->deviceToken->type ?? "" : "",
                    'Device App Version'    =>  $user->deviceToken ? $user->deviceToken->app_version ?? "" : "",
                    'Device OS Name'        =>  $user->deviceToken ? $user->deviceToken->os_name ?? "" : "",
                    'Device OS Version'     =>  $user->deviceToken ? $user->deviceToken->os_version ?? "" : "",
                    'Subscription plan'     =>  $user->subscription ? $user->subscription->subscriptionPlan ? ($user->subscription->subscriptionPlan->subscriptionPlanTranslation ? $user->subscription->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "" : "",
                    'Subscription month'    =>  $user->subscription ? $user->subscription->months ? : "" : "" ,
                    'Subscription amount'   =>  $user->subscription ? $user->subscription->amount ? : "" : "" ,
                    'Subscription end date' =>  $user->subscription ? $user->subscription->end_date ? : "" : "" ,
                    'Subscription status'   =>  $user->subscription ? $user->subscription->status ? : "" : "" ,
                    'Active'                =>  $user->is_active == 'y' ? 'y' : 'n'
                ];
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array(
                'Account Id', 'Name', 'Email', 'Birth Date', 'Contact No', 'Verify Video Status', 'Verify Photo Status', 'Gender', 'Location', 
                'Intrest', 'Verify Status', 'Profile Percentage', 'Language', 'Langauge Code', 'Swipe Count', 'Like Count', 'Match Count', 
                'Chat Count', 'Is Social User', 'Is Subscribed', 'Subscription End Date', 'Email Verified Date', 'Contact Verified Date',
                'Photo Verified Date', 'Video Verified Date', 'Device Name', 'Device Type', 'Device App Version', 'Device OS Name', 'Device OS Version',
                'Subscription plan','Subscription month','Subscription amount','Subscription status','Active'
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'], $row['Name'], $row['Email'], $row['Birth Date'], $row['Contact No'], $row['Verify Video Status'], $row['Verify Photo Status'], $row['Gender'], $row['Location'], $row['Intrest'], $row['Verify Status'], $row['Profile Percentage'], $row['Language'], $row['Langauge Code'], $row['Swipe Count'], $row['Like Count'], $row['Match Count'], $row['Chat Count'], $row['Is Social User'], $row['Is Subscribed'], $row['Subscription End Date'], $row['Email Verified Date'], $row['Contact Verified Date'], $row['Photo Verified Date'], $row['Video Verified Date'], $row['Device Name'], $row['Device Type'], $row['Device App Version'], $row['Device OS Name'], $row['Device OS Version'], 
                    $row['Subscription plan'],$row['Subscription month'],$row['Subscription amount'],$row['Subscription status'], $row['Active'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate user csv. Try again later')->error();
        }
        return redirect(route('admin.users.index'));
    }

    public function unde_review()
    {
        return view('admin.pages.users.unde_review')->with(['custom_title' => 'Profile Under Review']);
    }

    public function csvDownloadUndeReview(Request $request)
    {
        $down_file_name = 'User Unde Review';
        $users = User::with('userTransEn', 'deviceToken', 'country', 'location', 'language','subscription.subscriptionPlan.subscriptionPlanTranslation')
            ->orderBy('created_at','desc');
        $users->where('verify_photo_status', '=' , 'under_review')->where('verify_photo', '!=' , '');    
        $users = $users->get();
        // echo "<pre>"; print_r($users->toArray()); die();
        if (!$users->isEmpty()) {
            foreach ($users as $user) {
                $data[] = [
                    'Account Id'            =>  $user->account_id ?? "",
                    'Name'                  =>  $user->userTransEn ? $user->userTransEn->full_name ?? "" : "",
                    'Email'                 =>  $user->email ?? "",
                    'Birth Date'            =>  $user->birth_date,
                    'Contact No'            =>  $user->country_code." ".$user->contact_no ?? "",
                    'Verify Video Status'   =>  $user->verify_video_status ?? "",
                    'Verify Photo Status'   =>  $user->verify_photo_status ?? "",
                    'Gender'                =>  $user->gender ?? "",
                    'Location'              =>  $user->location->name ?? "",
                    'Intrest'               =>  $user->interest ?? "",
                    'Verify Status'         =>  $user->verify_status ?? "",
                    'Profile Percentage'    =>  $user->profile_percentage ?? "",
                    'Language'              =>  $user->language ? $user->language->language ?? "" : "",
                    'Langauge Code'         =>  $user->language ? $user->language->lang_code ?? "" : "",
                    'Swipe Count'           =>  $user->swipe_count ?? "",
                    'Like Count'            =>  $user->like_count ?? "",
                    'Match Count'           =>  $user->match_count ?? "",
                    'Chat Count'            =>  $user->chat_count ?? "",
                    'Is Social User'        =>  $user->is_social_user ?? "",
                    'Is Subscribed'         =>  $user->is_subscribed ?? "",
                    'Subscription End Date' =>  $user->subscription_end_date ?? "",
                    'Email Verified Date'   =>  $user->email_verified_at ?? "",
                    'Contact Verified Date' =>  $user->contact_verified_at ?? "",
                    'Photo Verified Date'   =>  $user->photo_verified_at ?? "",
                    'Video Verified Date'   =>  $user->video_verified_at ?? "",
                    'Device Name'           =>  $user->deviceToken ? $user->deviceToken->device_name ?? "" : "",
                    'Device Type'           =>  $user->deviceToken ? $user->deviceToken->type ?? "" : "",
                    'Device App Version'    =>  $user->deviceToken ? $user->deviceToken->app_version ?? "" : "",
                    'Device OS Name'        =>  $user->deviceToken ? $user->deviceToken->os_name ?? "" : "",
                    'Device OS Version'     =>  $user->deviceToken ? $user->deviceToken->os_version ?? "" : "",
                    'Subscription plan'     =>  $user->subscription ? $user->subscription->subscriptionPlan ? ($user->subscription->subscriptionPlan->subscriptionPlanTranslation ? $user->subscription->subscriptionPlan->subscriptionPlanTranslation->name : "N/A") : "" : "",
                    'Subscription month'    =>  $user->subscription ? $user->subscription->months ? : "" : "" ,
                    'Subscription amount'   =>  $user->subscription ? $user->subscription->amount ? : "" : "" ,
                    'Subscription end date' =>  $user->subscription ? $user->subscription->end_date ? : "" : "" ,
                    'Subscription status'   =>  $user->subscription ? $user->subscription->status ? : "" : "" ,
                    'Active'                =>  $user->is_active == 'y' ? 'y' : 'n'
                ];
            }

            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            fputcsv($handle, array(
                'Account Id', 'Name', 'Email', 'Birth Date', 'Contact No', 'Verify Video Status', 'Verify Photo Status', 'Gender', 'Location', 
                'Intrest', 'Verify Status', 'Profile Percentage', 'Language', 'Langauge Code', 'Swipe Count', 'Like Count', 'Match Count', 
                'Chat Count', 'Is Social User', 'Is Subscribed', 'Subscription End Date', 'Email Verified Date', 'Contact Verified Date',
                'Photo Verified Date', 'Video Verified Date', 'Device Name', 'Device Type', 'Device App Version', 'Device OS Name', 'Device OS Version',
                'Subscription plan','Subscription month','Subscription amount','Subscription status','Active'
            ));

            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['Account Id'], $row['Name'], $row['Email'], $row['Birth Date'], $row['Contact No'], $row['Verify Video Status'], $row['Verify Photo Status'], $row['Gender'], $row['Location'], $row['Intrest'], $row['Verify Status'], $row['Profile Percentage'], $row['Language'], $row['Langauge Code'], $row['Swipe Count'], $row['Like Count'], $row['Match Count'], $row['Chat Count'], $row['Is Social User'], $row['Is Subscribed'], $row['Subscription End Date'], $row['Email Verified Date'], $row['Contact Verified Date'], $row['Photo Verified Date'], $row['Video Verified Date'], $row['Device Name'], $row['Device Type'], $row['Device App Version'], $row['Device OS Name'], $row['Device OS Version'], 
                    $row['Subscription plan'],$row['Subscription month'],$row['Subscription amount'],$row['Subscription status'], $row['Active'],
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate user csv. Try again later')->error();
        }
        return redirect(route('admin.users.index'));
    }

    public function gender_update(Request $request) {
        
        // echo "<pre>"; print_r($request->all()); die();

        if ($request->id != '' && $request->gender != '') {
            $users = User::select('id','gender','is_subscribed','subscription_end_date')->where('id',$request->id)->first();
            if ($users != '') {
                if ($request->gender != $users->gender) 
                {
                    if ($users->gender == "Male" && $request->gender == "Female") {
                        $free_subscription = config('utility.subscription.free_for_girls');
                        if($free_subscription && $request->gender == 'Female'){

                            // create modedl object and used this function
                            // $users->gender = ;
                            // $users = $this->user->buyFreeSubscription();
                            // $users->save();

                            $plan = SubscriptionPlan::where('is_default_for_girl','y')->first();
                            if($plan){

                                $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                                if ($users->subscription_end_date >= $new_subscription_start_date) {
                                    $new_subscription_start_date = $users->subscription_end_date;
                                }

                                // add free subscription
                                Subscription::firstOrCreate([
                                    'user_id'       =>  $users->id ?? NULL,
                                    'plan_id'       =>  $plan->id ?? NULL,
                                    'months'        =>  $plan->months,
                                    'amount'        =>  $plan->amount,
                                    'start_date'    =>  $new_subscription_start_date,
                                    'end_date'      =>  NULL,
                                    'payment_date'  =>  now(),
                                    'payment_type'  =>  '',
                                    'status'        =>  'active',
                                ], [
                                    'custom_id'     =>  getUniqueString('subscriptions'),
                                ]);

                                // update user table subscription details
                                User::where('id',$request->id)->update([ 
                                    'gender' =>  $request->gender ?? $users->gender,
                                    'is_subscribed' =>  'y',
                                ]);
                            }
                        }

                        $content['status'] = 200;
                        $content['message'] = "Gender updated successfully1.";
                        return response()->json($content);
                    }

                    if ($users->gender == "Female" && $request->gender == "Male") {

                        // delete subscription data
                        Subscription::where('user_id',$users->id)->delete();

                        // update user table subscription details
                        User::where('id',$request->id)->update([ 
                            'gender' =>  $request->gender ?? $users->gender,
                            'is_subscribed' =>  'n',
                            'subscription_end_date' =>  NULL,
                        ]);

                        $content['status'] = 200;
                        $content['message'] = "Gender updated successfully1.";
                        return response()->json($content);
                    }
                    
                }
                else
                {
                    $content['status'] = 200;
                    $content['message'] = "Gender updated successfully3.";
                    return response()->json($content);
                }
            }
            else
            {
                $content['status'] = 404;
                $content['message'] = "User not found.";
                return response()->json($content);
            }
        }
        else
        {
            $content['status'] = 404;
            $content['message'] = "Messing required paramater..!";
            return response()->json($content);
        }
        // try {
        //     $content['status'] = 200;
        //     $content['message'] = "Status updated successfully.";
        //     return response()->json($content);
        // } catch (QueryException $e) {
        //     DB::rollback();
        //     return redirect()->back()->flash('error', $e->getMessage());
        // } catch (Exception $e) {
        //     return redirect()->back()->with('error', $e->getMessage());
        // }
    }

    public function bulk_gender_update(Request $request) {
            

        $user_id_arr            = explode(",",$request->multi_user_id);
        

        if (!empty($user_id_arr)) {

            for($i = 0; $i< count($user_id_arr); $i++) {

                    $req_user_id = $user_id_arr[$i];
                    $req_gender  = $request->target_gender;

                    $users = User::select('id','gender','is_subscribed','subscription_end_date')->where('custom_id',$req_user_id)->first();
                    if ($users != '') {
                        if ($req_gender != $users->gender) 
                        {
                            if (($users->gender == "Male" && $req_gender == "Female") || ($users->gender == "" && $req_gender == "Female")) {

                                
                                /*$free_subscription = config('utility.subscription.free_for_girls');
                                if($free_subscription && $req_gender == 'Female') {

                                    // create modedl object and used this function
                                    // $users->gender = ;
                                    // $users = $this->user->buyFreeSubscription();
                                    // $users->save();

                                    $plan = SubscriptionPlan::where('is_default_for_girl','y')->first();
                                    if($plan){

                                        $new_subscription_start_date = \Carbon\Carbon::today()->format('Y-m-d');
                                        if ($users->subscription_end_date >= $new_subscription_start_date) {
                                            $new_subscription_start_date = $users->subscription_end_date;
                                        }

                                        // add free subscription
                                        Subscription::firstOrCreate([
                                            'user_id'       =>  $users->id ?? NULL,
                                            'plan_id'       =>  $plan->id ?? NULL,
                                            'months'        =>  $plan->months,
                                            'amount'        =>  $plan->amount,
                                            'start_date'    =>  $new_subscription_start_date,
                                            'end_date'      =>  NULL,
                                            'payment_date'  =>  now(),
                                            'payment_type'  =>  '',
                                            'status'        =>  'active',
                                        ], [
                                            'custom_id'     =>  getUniqueString('subscriptions'),
                                        ]);

                                        // update user table subscription details
                                        User::where('custom_id',$req_user_id)->update([ 
                                            'gender' =>  $req_gender ?? $users->gender,
                                            'is_subscribed' =>  'y',
                                        ]);
                                    }
                                }*/

                                // update user table gender details
                                User::where('custom_id',$req_user_id)->update([
                                    'gender' =>  $req_gender ?? $users->gender
                                ]);
                            }

                            if (($users->gender == "Female" && $req_gender == "Male") || ($users->gender == "" && $req_gender == "Male")) {

                                // delete subscription data
                                Subscription::where('user_id',$users->id)->delete();

                                // update user table subscription details
                                User::where('custom_id',$req_user_id)->update([ 
                                    'gender' =>  $req_gender ?? $users->gender,
                                    'is_subscribed' =>  'n',
                                    'subscription_end_date' =>  NULL,
                                ]);
                                
                            }
                            
                        }
                        
                    }
                    else
                    {
                        $content['status'] = 404;
                        $content['message'] = "User not found.";
                        return response()->json($content);
                    }
            }

            $content['status'] = 200;
            $content['message'] = "Gender updated successfully1.";
            return response()->json($content);

        }
        else
        {
            $content['status'] = 404;
            $content['message'] = "Messing required paramater..!";
            return response()->json($content);
        }
        // try {
        //     $content['status'] = 200;
        //     $content['message'] = "Status updated successfully.";
        //     return response()->json($content);
        // } catch (QueryException $e) {
        //     DB::rollback();
        //     return redirect()->back()->flash('error', $e->getMessage());
        // } catch (Exception $e) {
        //     return redirect()->back()->with('error', $e->getMessage());
        // }
    }

    // ST - For Bulk Photo Verification
    public function bulk_photo_verification(Request $request) {

        $user_id_arr = explode(",",$request->multi_user_id);
        $req_gender  = $request->verify_photo_status;

        if (!empty($user_id_arr)) {

            for($i = 0; $i< count($user_id_arr); $i++) {

                // update user table gender details
                User::where('custom_id',$user_id_arr[$i])->update([
                    'verify_photo_status' =>  'verified',
                    'photo_verified_at'   =>  \Carbon\Carbon::now()
                ]);

                $user = User::where('custom_id','=',$user_id_arr[$i])->first();
                $user->calculateProfilePercent();
            }
        }

        $content['status'] = 200;
        $content['message'] = "Photo verification done!";
        return response()->json($content);
    }
    // EN - For Bulk Photo Verification

}