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
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\NotificationJob;

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
        $personalities = Personality::with('personalityTransDefault')->where('is_active','y')->get();
        $attributes = ProfileDetail::with(['profileDetailTransDefault'])->where(['is_active'=>'y'])->get();
        $interests = Interest::with(['subInterests.interestTransDefault'])->where(['is_active'=>'y'])->get();
        $countries = Country::where(['is_active'=>'y'])->get();
        $locations = Location::with(['locationTransDefault'])->where(['is_active'=>'y'])->get();
        $languages = Language::where(['is_active'=>'y'])->get();
        return view('admin.pages.users.create',compact('personalities','interests','countries','attributes','locations','languages'))->with(['custom_title' => 'User']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try{
            DB::beginTransaction();
            $path = NULL; $traslate_data = [];
            if( $request->has('profile_photo') ) {
                $path = $request->file('profile_photo')->store('users/profile_photo');
            }

            $user = User::create($request->all());
            $user['custom_id']   =  getUniqueString('users');
            $user['password']    =  Hash::make(config('utility.default_password'));
            $user->profile_photo =  $path;

            if(!empty($request->country_code)){
                $country = Country::wherePhonecode($request->country_code)->whereIsActive('y')->firstOrFail();
                $user->country_id = $country->id;
            }
            if(!empty($request->location)){
                $location = Location::whereId($request->location)->whereIsActive('y')->firstOrFail();
                $user->location_id = $location->id;
                $user->discover_location_id = $location->id;
            }
            if(!empty($request->language)){
                $language = Language::whereLangCode($request->language)->whereIsActive('y')->firstOrFail();
                $user->language_id = $language->id;
            }

            // Store Account Id
            if(!empty($request->language) && $request->language == 'en'){
                $user->account_id = Str::slug(substr($request->full_name, 0, 4), "_").'_'.time();
            }

            // Full name
            $language_codes = Language::pluck('lang_code')->toArray();
            if(!empty($request->full_name)) {
                foreach($language_codes as $language_code){
                    $traslate_data[$language_code] =  [ 'full_name' =>  $request->full_name ];
                }
                $user->update($traslate_data);
                $user->is_trans_full_name = 'n';
            }

            if(!empty($request->fav_movie)){
                foreach($language_codes as $language_code){
                    $traslate_data[$language_code] =  [ 'fav_movie' =>  $request->fav_movie ];
                }
                $user->update($traslate_data);
                $user->is_trans_fav_movie = 'n';
            }

            if(!empty($request->about_me)){
                foreach($language_codes as $language_code){
                    $traslate_data[$language_code] =  [ 'about_me' =>  $request->about_me ];
                }
                $user->update($traslate_data);
                $user->is_trans_about_me = 'n';
            }

            // Set Contact Number As Verified
            if(!empty($user->contact_no)){ $user->contact_verified_at = \Carbon\Carbon::now();  }

            /* Verification Details */
            $verify_photo   =    $request->verify_photo;
            $verify_video   =    $request->verify_video;

            if($verify_photo == 'y' && empty($user->photo_verified_at)){
                $user->photo_verified_at = \Carbon\Carbon::now(); 
            }elseif($verify_photo == NULL){
                $user->photo_verified_at = NULL;
            }

            if($verify_video == 'y' && empty($user->video_verified_at)){ 
                $user->video_verified_at = \Carbon\Carbon::now(); 
            }elseif($verify_video == NULL){
                $user->video_verified_at = NULL;
            }

            // Set Default Discover
            $user->discover_distance    = config('utility.profile.detail.discover_distance');
            $user->discover_start_age   = config('utility.profile.detail.discover_start_age');
            $user->discover_end_age     = config('utility.profile.detail.discover_end_age');

            /* User Personality */
            if(!empty($request->personalities)){
                foreach($request->personalities as $personality_id){
                    UserPersonality::updateOrCreate([
                      'user_id'         => $user->id,
                      'personality_id'  => $personality_id,  
                    ],[
                        'custom_id'     => getUniqueString('user_personalities'),
                    ]);
                }
            }

            /* User Interest */
            if(!empty($request->traveling_id)){
                foreach($request->traveling_id as $traveling){
                    UserInterest::updateOrCreate([
                      'user_id'  => $user->id,
                      'interest_id' => $traveling,  
                    ],[
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if(!empty($request->music_id)){
                foreach($request->music_id as $music){
                    UserInterest::updateOrCreate([
                        'user_id' => $user->id,
                        'interest_id' => $music,  
                    ],[
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if(!empty($request->hobbie_id)){
                foreach($request->hobbie_id as $hobby){
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $hobby,
                    ],[
                        'custom_id' => getUniqueString('user_interests'),  
                    ]);
                }
            }
            if(!empty($request->game_id)){
                foreach($request->game_id as $game){
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $game,  
                    ],[
                        'custom_id' => getUniqueString('user_interests'),
                    ]);
                }
            }
            if(!empty($request->sport_id)){
                foreach($request->sport_id as $sport){
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $sport,
                    ],[
                        'custom_id' => getUniqueString('user_interests'), 
                    ]);
                }
            }
            if(!empty($request->food_id)){
                foreach($request->food_id as $food){
                    UserInterest::updateOrCreate([
                        'user_id'  => $user->id,
                        'interest_id' => $food, 
                    ],[ 
                        'custom_id' => getUniqueString('user_interests'), 
                    ]);
                }
            }
            if(!empty($request->depend_id)){
                foreach($request->depend_id as $depend){
                    UserInterest::updateOrCreate([
                       'user_id'  => $user->id, 
                       'interest_id' => $depend,
                    ],[ 
                        
                        'custom_id' => getUniqueString('user_interests'),   
                    ]);
                }
            }
            if(!empty($request->singer_id)){
                foreach($request->singer_id as $singer){
                    UserInterest::updateOrCreate([
                       'user_id'  => $user->id, 
                       'interest_id' => $singer,
                    ],[ 
                        'custom_id' => getUniqueString('user_interests'),    
                    ]);
                }
            }

            $user->profile_percentage = $user->calculateProfilePercent();

            if( $user->save() ) {
                DB::commit();
                flash('User account created successfully!')->success();
            } else {
                flash('Unable to save avatar. Please try again later.')->error();
            }
            return redirect(route('admin.users.index'));
        }catch(QueryException $e){
            DB::rollback();
            return redirect()->back()->flash('error',$e->getMessage());
        }catch(Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
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
            'userTranslation','userTransDefault','userDetails',
            'country.countryTransDefault','location.locationTransDefault','language',
            'interests.interest.interestTransDefault','religion.profileDetailTransDefault',
            'relationshipStatus.profileDetailTransDefault','youAreHere.profileDetailTransDefault',
            'foodPreference.profileDetailTransDefault','drinking.profileDetailTransDefault',
            'smoking.profileDetailTransDefault','starSign.profileDetailTransDefault',
            'religion.profileDetailTransDefault','community.profileDetailTransDefault',
            'education.profileDetailTransDefault',
            'university.profileDetailTransDefault','profession.profileDetailTransDefault',
            'personalities.personality.personalityTransDefault'
            ])->whereId($user->id)->firstOrFail();
        return view('admin.pages.users.view',compact('user'))->with(['custom_title' => 'User']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = User::with('userTransDefault')->whereId($user->id)->firstOrFail();
        $personalities = Personality::with('personalityTransDefault')->where('is_active','y')->get();
        $attributes = ProfileDetail::with(['profileDetailTransDefault'])->where(['is_active'=>'y'])->get();
        $interests = Interest::with(['subInterests.interestTransDefault','subInterests.subInterests.interestTransDefault'])
                        ->where(['is_active'=>'y'])->get();
        $countries = Country::where(['is_active'=>'y'])->get();
        $locations = Location::with(['locationTransDefault'])->where(['is_active'=>'y'])->get();
        $languages = Language::where(['is_active'=>'y'])->get();

        //user interest
        $user_interest = UserInterest::where('user_id',$user->id)->pluck('interest_id')->toArray();
        $user_personality = UserPersonality::where('user_id',$user->id)->pluck('personality_id')->toArray();
        return view('admin.pages.users.edit', compact('user','user_personality','personalities','user_interest','interests','attributes','countries','locations','languages'))->with(['custom_title' => 'Users']);
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
        try{
            DB::beginTransaction();
            if(!empty($request->action) && $request->action == 'change_status') {
                $content = ['status'=>204, 'message'=>"something went wrong"];
                if($user) {
                    $user->is_active = $request->value;
                    if($user->save()) {
                        DB::commit();
                        $content['status']=200;
                        $content['message'] = "Status updated successfully.";
                    }
                }
                return response()->json($content);
            } else {
                $verify_notify = $verify_photo_notify = $verify_video_notify = false; 
                if($user->verify_status == 'under_review'){ $verify_notify = true;  }
                if($user->verify_photo_status != 'unverified'){ $verify_photo_notify = true;  }
                if($user->verify_video_status != 'unverified'){ $verify_video_notify = true;  }

                $path = $user->profile_photo; $not_to_delete_interest = $not_to_delete_personality = array();

                //request has remove_profie_photo then delete user image
                if( $request->has('remove_profie_photo') ){
                    if( $user->profile_photo){ Storage::delete($user->profile_photo); }
                    $path = null;
                }

                if( $request->hasFile('profile_photo') ) {
                    if( $user->profile_photo){ Storage::delete($user->profile_photo); }
                    $path = $request->profile_photo->store('users/profile_photo');
                    $user->is_media_checked = 'n';
                }
                $user->fill($request->validated());
                $user->profile_photo = $path;

                if(!empty($request->country_code)){
                    $country = Country::wherePhonecode($request->country_code)->whereIsActive('y')->firstOrFail();
                    $user->country_id = $country->id;
                }
                if(!empty($request->location)){
                    $location = Location::whereId($request->location)->whereIsActive('y')->firstOrFail();
                    $user->location_id = $location->id;
                    $user->discover_location_id = $location->id;
                }
                if(!empty($request->language)){
                    $language = Language::whereLangCode($request->language)->whereIsActive('y')->firstOrFail();
                    $user->language_id = $language->id;
                }

                // Store Account Id
                if(!empty($request->language) && $request->language == 'en'){
                    $user->account_id = Str::slug(substr($request->full_name, 0, 4), "_").'_'.time();
                }

                // Full name
                $language_codes = Language::pluck('lang_code')->toArray();
                if(!empty($request->full_name)) {
                    foreach($language_codes as $language_code){
                        $traslate_data[$language_code] =  [ 'full_name' =>  $request->full_name ];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_full_name = 'n';
                }

                if(!empty($request->fav_movie)){
                    foreach($language_codes as $language_code){
                        $traslate_data[$language_code] =  [ 'fav_movie' =>  $request->fav_movie ];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_fav_movie = 'n';
                }

                if(!empty($request->about_me)){
                    foreach($language_codes as $language_code){
                        $traslate_data[$language_code] =  [ 'about_me' =>  $request->about_me ];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_about_me = 'n';
                }

                /* Verification Details */
                $photo_verified_at  =   $request->photo_verified_at;
                $video_verified_at  =   $request->video_verified_at;

                if($photo_verified_at == 'y' && empty($user->photo_verified_at)){
                    $user->photo_verified_at = \Carbon\Carbon::now(); 
                }elseif($photo_verified_at == NULL){
                    $user->photo_verified_at = NULL;
                }

                if($video_verified_at == 'y' && empty($user->video_verified_at)){ 
                    $user->video_verified_at = \Carbon\Carbon::now(); 
                }elseif($video_verified_at == NULL){
                    $user->video_verified_at = NULL;
                }

                 /* User Personality */
                if(!empty($request->personalities)){
                    foreach($request->personalities as $personality_id){
                        $custom_id = getUniqueString('user_personalities');
                        UserPersonality::updateOrCreate([
                              'user_id'         => $user->id,
                              'personality_id'  => $personality_id,  
                            ],[
                                'custom_id'     => $custom_id,
                            ]);
                       $not_to_delete_personality[] = $custom_id;
                    }
                }
                UserPersonality::where('user_id',$user->id)->whereNotIn('custom_id',$not_to_delete_personality)->delete();

                /* User Interest */
                if(!empty($request->traveling_id)){
                    foreach($request->traveling_id as $traveling){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                              'user_id'  => $user->id,
                              'interest_id' => $traveling,  
                            ],[
                                'custom_id' => $custom_id,
                            ]);
                       $not_to_delete_interest[] = $custom_id;
                    }
                }

                if(!empty($request->music_id)){
                    foreach($request->music_id as $music){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                                'user_id' => $user->id,
                                'interest_id' => $music,  
                            ],[
                                'custom_id' => $custom_id,
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if(!empty($request->hobbie_id)){
                    foreach($request->hobbie_id as $hobby){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                                'user_id'  => $user->id,
                                'interest_id' => $hobby,
                            ],[
                                'custom_id' => $custom_id,  
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if(!empty($request->game_id)){
                    foreach($request->game_id as $game){
                    $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                                'user_id'  => $user->id,
                                'interest_id' => $game,  
                            ],[
                                'custom_id' => $custom_id,
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if(!empty($request->sport_id)){
                    foreach($request->sport_id as $sport){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                                'user_id'  => $user->id,
                                'interest_id' => $sport,
                            ],[
                                'custom_id' => $custom_id, 
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if(!empty($request->food_id)){
                    foreach($request->food_id as $food){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                                'user_id'  => $user->id,
                                'interest_id' => $food, 
                            ],[ 
                                'custom_id' => $custom_id, 
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                if(!empty($request->depend_id)){
                    foreach($request->depend_id as $depend){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                               'user_id'  => $user->id, 
                               'interest_id' => $depend,
                            ],[ 
                                
                                'custom_id' => $custom_id,   
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }

                if(!empty($request->singer_id)){
                    foreach($request->singer_id as $singer){
                        $custom_id = getUniqueString('user_interests');
                        UserInterest::updateOrCreate([
                               'user_id'  => $user->id, 
                               'interest_id' => $singer,
                            ],[ 
                                'custom_id' => $custom_id,    
                            ]);
                        $not_to_delete_interest[] = $custom_id;
                    }
                }
                
                UserInterest::where('user_id',$user->id)->whereNotIn('custom_id',$not_to_delete_interest)->delete();

                $user->profile_percentage = $user->calculateProfilePercent();

                if( $user->save() ) {
                    // Notify Profile Verification
                    if($verify_notify && $user->verify_status != 'under_review'){
                        if($user->verify_status == 'verified'){
                            $title = trans('api.notify_message.profile_verified.title');
                            $message = trans('api.notify_message.profile_verified.message');
                            $type = config('utility.notification.type.profile_verified');
                        }else{
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
                        $notificationJob = new NotificationJob($notification, $user);
                        dispatch($notificationJob);
                    }

                    // Notify Photo
                    if($verify_photo_notify && $user->verify_photo_status == 'unverified'){
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
                        $notificationJob = new NotificationJob($notification, $user);
                        dispatch($notificationJob);
                    }

                    // Notify Video
                    if($verify_video_notify && $user->verify_video_status == 'unverified'){
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
        }catch(QueryException $e){
            DB::rollback();
            return redirect()->back()->flash('error',$e->getMessage());
        }catch(Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
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
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];

            // $users_profile_photos = User::whereIn('custom_id', explode(',', $request->ids))->pluck('profile_photo')->toArray();
            // foreach ($users_profile_photos as $image) {
            //     if(!empty($image)){
            //       Storage::delete($image);
            //     }
            // }
            User::whereIn('custom_id',explode(',',$request->ids))->forceDelete();
            $content['status']=200;
            $content['message'] = "User deleted successfully.";
            $content['count'] = User::all()->count();
            return response()->json($content);
        }else{
            $user = User::where('custom_id', $id)->firstOrFail();
            // if( $user->profile_photo ){
            //     Storage::delete($user->profile_photo);
            // }
            $user->forceDelete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"User deleted successfully.", 'count' => User::all()->count());
                return response()->json($content);
            }else{
                flash('User deleted successfully.')->success();
                return redirect()->route('admin.users.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $users = User::with('userTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->Where('account_id', 'like', "%{$search}%")
                    ->orWhere('profile_percentage', 'like', "%{$search}%")
                    ->orWhere('country_code', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('interest', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('userTransDefault',function($q) use ($search){
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        $count = $users->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->get();
        foreach ($users as $user) {

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'getaction' => $user->is_active,
                'class' => '',
                'id' => $user->custom_id,
            ];

            $records['data'][] = [
                'id' => $user->id,
                'account_id' => $user->account_id ?? "N/A",
                'full_name' =>  $user->userTransDefault ? $user->userTransDefault->full_name : "N/A",
                'profile_percentage' =>  $user->profile_percentage ?? 0,
                'contact_no' => $user->contact_no ? '<a href="tel:' .$user->country_code.''.$user->contact_no.'" >' .$user->country_code.''. $user->contact_no . '</a>' : 'N/A',
                'email' => $user->email ? '<a href="mailto:' .$user->email. '" >' .$user->email. '</a>' : 'N/A',
                'gender' => $user->gender ?? 'N/A',
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $user->custom_id], $user)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $user->custom_id)->render(),
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
        // dd($records);

        return $records;
    }

    public function getActorList(Request $request)
    {
        $interests = Interest::with('interestTransDefault')->where('parent_id',$request->actor_id)->get();
        $data = '';
        if($interests->isNotEmpty()){
            foreach($interests as $interest){
                if($interest->interestTransDefault){
                    $data .= '<option value='.$interest->id.'>'.$interest->interestTransDefault->title.'</option>';
                }
            }
        }
        return response()->json($data);
    }

    public function getSingerList(Request $request)
    {
        $interests = Interest::with('interestTransDefault')->where('parent_id',$request->singer_id)->get();
        $data = '';
        if($interests->isNotEmpty()){
            foreach($interests as $interest){
                if($interest->interestTransDefault){
                    $data .= '<option value='.$interest->id.'>'.$interest->interestTransDefault->title.'</option>';
                }
            }
        }
        return response()->json($data);
    }
}
