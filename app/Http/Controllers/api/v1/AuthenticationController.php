<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Resources\v1\ { UserProfile };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Storage, Auth, Hash };
use App\Http\Requests\Api\Authentication\ { LoginRequest, RegisterRequest, SocialLoginRequest };
use App\Http\Requests\Api\User\ { FullProfileRequest };
use App\Models\ { User, Country, UserDetail, Location, Interest, UserInterest, Language, ProfileDetail, UserFestival, UserPet };

class AuthenticationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // User Login
    public function login(Request $request)
    {   
        $rules = LoginRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            $this->response['meta']['message']  = trans('api.login_fail');
            $this->status = Response::HTTP_FORBIDDEN;

            $checksumDetails = $this->validateCheckSum($request->security_token, $request->contact_no);
            if( $checksumDetails->validate ) {
                try {
                    $user = User::whereContactNo($request->contact_no)->withCount('likes')->firstOrFail();
                    if($user->is_active == 'y'){
                        Auth::login($user);
                        return (new UserProfile($user))
                            ->additional([
                                'meta' => [
                                    'message'           =>  trans('api.login'),
                                    'auth_token'        =>  $user->createToken(config('utility.token'))->plainTextToken,
                                ] ]);
                    }else{
                        $this->response['meta']['message']  = trans('api.in_active');
                    }
                } catch (\Exception $e) {
                    $this->storeErrorLog($e,'login',trans('api.login_fail'));
                }
            } else {
                $this->response['meta']['message']  = $checksumDetails->message;
            }
        }
        return $this->returnResponse();
    }

    // Signup/Profile Setup For User
    public function setProfile(Request $request)
    {
        $rules = RegisterRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $country_id = $location_id = $language_id = NULL;
                if(!empty($request->country_code)){
                    $country = Country::wherePhonecode($request->country_code)->whereIsActive('y')->firstOrFail();
                    $country_id = $country->id;
                }
                if(!empty($request->location)){
                    $location = Location::whereCustomId($request->location)->whereIsActive('y')->firstOrFail();
                    $location_id = $location->id;
                }
                if(!empty($request->language)){
                    $language = Language::whereLangCode($request->language)->whereIsActive('y')->firstOrFail();
                    $language_id = $language->id;
                }
                if(!empty($request->email)){
                    $user = User::whereEmail($request->email)->first();
                }
                
                if(!empty($user)){
                    $user->fill($request->all());
                }else{
                    $user = User::updateOrCreate([
                        'country_code'      =>  $request->country_code ?? NULL,
                        'contact_no'        =>  $request->contact_no ?? NULL,
                    ],[
                        'custom_id'         =>  getUniqueString('users'),
                        'full_name'         =>  $request->full_name ?? NULL,
                        'birth_date'        =>  $request->birth_date ?? NULL,
                        'gender'            =>  $request->gender ?? NULL,
                        'interest'          =>  $request->interest ?? NULL,
                        'country_id'        =>  $country_id ?? NULL,
                        'location_id'       =>  $location_id ?? NULL,
                        'language_id'       =>  $language_id ?? NULL,
                        'password'          =>  Hash::make(config('utility.default_password')),
                    ]);
                }

                if( !empty($request->profile_photo) ) {
                    if(!empty($user->profile_photo)){
                        if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                    }
                    $path = $request->file('profile_photo')->store('users/profile_photo');
                    $user->profile_photo = $path;
                }

                if($user->save()){
                    Auth::login($user);
                    return (new UserProfile($user))
                        ->additional([
                            'meta' => [
                                'message'       =>  trans('api.profile_setuped'), 
                                'auth_token'    =>  $user->createToken(config('utility.token'))->plainTextToken,
                            ]
                        ]);
                }else{
                    $this->response['meta']['message']  = trans('api.profile_setuped_fail');
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    case 'App\Models\Country':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Country")]);
                        break;
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Location")]);
                        break;
                    case 'App\Models\UserDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'set_profile');
            }
        }

        return $this->returnResponse();
    }

    public function setFullProfile(Request $request){
        $rules = FullProfileRequest::rules($request);
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = Auth::user();
                $relationship_status = ProfileDetail::select('id')->whereSlug($request->relationship_status)->whereIsActive('y')->firstOrFail();
                $you_are_here = ProfileDetail::select('id')->whereSlug($request->you_are_here)->whereIsActive('y')->firstOrFail();
                $food_preference = ProfileDetail::select('id')->whereSlug($request->food_preference)->whereIsActive('y')->firstOrFail();
                $drinking = ProfileDetail::select('id')->whereSlug($request->drinking)->whereIsActive('y')->firstOrFail();
                $smoking = ProfileDetail::select('id')->whereSlug($request->smoking)->whereIsActive('y')->firstOrFail();
                $star_sign = ProfileDetail::select('id')->whereSlug($request->star_sign)->whereIsActive('y')->firstOrFail();

                $religion = ProfileDetail::select('id')->whereSlug($request->religion)->whereIsActive('y')->first();
                $education = ProfileDetail::select('id')->whereSlug($request->education)->whereIsActive('y')->first();
                $occupation = ProfileDetail::select('id')->whereSlug($request->occupation)->whereIsActive('y')->first();
                $date_idea = ProfileDetail::select('id')->whereSlug($request->date_idea)->whereIsActive('y')->first();
                $social_cause = ProfileDetail::select('id')->whereSlug($request->social_cause)->whereIsActive('y')->first();
                $risk_taken = ProfileDetail::select('id')->whereSlug($request->risk_taken)->whereIsActive('y')->first();
                $perfect_relation_things = ProfileDetail::select('id')->whereSlug($request->perfect_relation_things)->whereIsActive('y')->first();
                $my_mantra = ProfileDetail::select('id')->whereSlug($request->my_mantra)->whereIsActive('y')->first();
                $one_thing_know = ProfileDetail::select('id')->whereSlug($request->one_thing_know)->whereIsActive('y')->first();
                $worst_date = ProfileDetail::select('id')->whereSlug($request->worst_date)->whereIsActive('y')->first();
                $introduce_to_family = ProfileDetail::select('id')->whereSlug($request->introduce_to_family)->whereIsActive('y')->first();
                $found_the_one = ProfileDetail::select('id')->whereSlug($request->found_the_one)->whereIsActive('y')->first();
                $about_me_surprises = ProfileDetail::select('id')->whereSlug($request->about_me_surprises)->whereIsActive('y')->first();
                $political_views = ProfileDetail::select('id')->whereSlug($request->political_views)->whereIsActive('y')->first();

                $user = $user->fill([
                    'email'                 =>  $request->email ? $request->email : $user->email,
                    'about_me'              =>  $request->about_me ? $request->about_me : $user->about_me,
                    'relationship_status_id'    =>  $relationship_status ? $relationship_status->id : NULL,
                    'you_are_here_id'           =>  $you_are_here ? $you_are_here->id : NULL,
                    'food_preference_id'        =>  $food_preference ? $food_preference->id : NULL,
                    'drinking_id'               =>  $drinking ? $drinking->id : NULL,
                    'smoking_id'                =>  $smoking ? $smoking->id : NULL,
                    'star_sign_id'              =>  $star_sign ? $star_sign->id : NULL,
                    'religion_id'               =>  $religion ? $religion->id : NULL,
                    // 'community_id'           =>  $community ? $community->id : NULL,
                    'education_id'              =>  $education ? $education->id : NULL,
                    'occupation_id'             =>  $occupation ? $occupation->id : NULL,
                    'date_idea_id'              =>  $date_idea ? $date_idea->id : NULL,
                    'social_cause_id'           =>  $social_cause ? $social_cause->id : NULL,
                    'risk_taken_id'             =>  $risk_taken ? $risk_taken->id : NULL,
                    'perfect_relation_id'       =>  $perfect_relation_things ? $perfect_relation_things->id : NULL,
                    'my_mantra_id'              =>  $my_mantra ? $my_mantra->id : NULL,
                    'one_thing_know_id'         =>  $one_thing_know ? $one_thing_know->id : NULL,
                    'worst_date_id'             =>  $worst_date ? $worst_date->id : NULL,
                    'intro_family_id'           =>  $introduce_to_family ? $introduce_to_family->id : NULL,
                    'found_one_id'              =>  $found_the_one ? $found_the_one->id : NULL,
                    'about_surprising_id'       =>  $about_me_surprises ? $about_me_surprises->id : NULL,
                    'political_view_id'         =>  $political_views ? $political_views->id : NULL,
                ]);
                if($user->save()){
                    if(!empty($request->interests)){
                        $selected_inerests = []; $not_delete_interests = [];
                        foreach($request->interests as $key => $interest_levels){
                            foreach($interest_levels as $key => $req_interest){
                                $selected_inerests[] = $req_interest;
                            }
                        }

                        if(count($selected_inerests) > 0){
                            $interest_ids = Interest::whereIn('custom_id',$selected_inerests)->whereIsActive('y')->pluck('id')->toArray();
                            foreach($interest_ids as $interest_id){
                                $custom_id = getUniqueString('user_interests');

                                UserInterest::updateOrCreate([
                                    'user_id'       =>  $user->id,
                                    'interest_id'   =>  $interest_id,
                                ],[
                                    'custom_id'     =>  $custom_id,
                                ]);
                                $not_delete_interests[] = $custom_id;
                            }
                        }

                        // Delete Interests
                        UserInterest::whereUserId($user->id)->whereNotIn('custom_id',$not_delete_interests)->delete();
                    }

                    if(!empty($request->fav_festivals)){
                        $not_delete_festivals = [];
                        $festival_ids = ProfileDetail::whereIn('slug',$request->fav_festivals)->whereIsActive('y')->pluck('id')->toArray();
                        foreach($festival_ids as $festival_id){
                            $custom_id = getUniqueString('user_festivals');

                            UserFestival::updateOrCreate([
                                'user_id'       =>  $user->id,
                                'festival_id'   =>  $festival_id,
                            ],[
                                'custom_id'     =>  $custom_id,
                            ]);

                            $not_delete_festivals[] = $custom_id;
                        }

                        // Delete Festivals
                        UserFestival::whereUserId($user->id)->whereNotIn('custom_id',$not_delete_festivals)->delete();
                    }

                    if(!empty($request->pets)){
                        $not_delete_pets = [];
                        $pet_ids = ProfileDetail::whereIn('slug',$request->pets)->whereIsActive('y')->pluck('id')->toArray();
                        foreach($pet_ids as $pet_id){
                            $custom_id = getUniqueString('user_pets');

                            UserPet::updateOrCreate([
                                'user_id'       =>  $user->id,
                                'pet_id'        =>  $pet_id,
                            ],[
                                'custom_id'     =>  $custom_id,
                            ]);

                            $not_delete_pets[] = $custom_id;
                        }

                        // Delete Pets
                        UserPet::whereUserId($user->id)->whereNotIn('custom_id',$not_delete_pets)->delete();
                    }

                    // Store Images
                    if($request->has('images')){
                        if($user->userDetails->isNotEmpty()){
                            foreach($user->userDetails as $userDetail){
                                if(!empty($userDetail->image)){
                                    if( Storage::exists($userDetail->image) ) { Storage::delete($userDetail->image); }
                                    $userDetail->delete();
                                }
                            }
                        }

                        $image_data = [];
                        foreach ($request->images as $key => $image) {
                            if($image){
                                $image_path = $image->store('users/images');
                                $image_data[] = [
                                    'custom_id'         =>  getUniqueString('user_details'),
                                    'user_id'           =>  $user->id,
                                    'image'             =>  $image_path,
                                    'created_at'        =>  \Carbon\Carbon::now(),
                                    'updated_at'        =>  \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        UserDetail::insert($image_data);
                    }
                    
                    // Store Videos
                    if($request->hasFile('videos')){
                        if($user->userDetails->isNotEmpty()){
                            foreach($user->userDetails as $userDetail){
                                if(!empty($userDetail->video)){
                                    if( Storage::exists($userDetail->video) ) { Storage::delete($userDetail->video); }
                                    $userDetail->delete();
                                }
                            }
                        }

                        $video_data = [];
                        foreach ($request->videos as $key => $video) {
                            if($video){
                                $video_path = $video->store('users/videos');
                                $video_data[] = [
                                    'custom_id'         =>  getUniqueString('user_details'),
                                    'user_id'           =>  $user->id,
                                    'video'             =>  $video_path,
                                    'created_at'        =>  \Carbon\Carbon::now(),
                                    'updated_at'        =>  \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        UserDetail::insert($video_data);
                    }   

                    // Store Audios
                    if(!empty($request->voices)){
                        if($user->userDetails->isNotEmpty()){
                            foreach($user->userDetails as $userDetail){
                                if(!empty($userDetail->voice)){
                                    if( Storage::exists($userDetail->voice) ) { Storage::delete($userDetail->voice); }
                                    $userDetail->delete();
                                }
                            }
                        }

                        $video_data = [];
                        foreach ($request->voices as $key => $voice) {
                            if($voice){
                                $voice_path = $voice->store('users/voice');
                                $video_data[] = [
                                    'custom_id'         =>  getUniqueString('user_details'),
                                    'user_id'           =>  $user->id,
                                    'voice'             =>  $voice_path,
                                    'created_at'        =>  \Carbon\Carbon::now(),
                                    'updated_at'        =>  \Carbon\Carbon::now(),
                                ];
                            }
                        }
                        UserDetail::insert($video_data);
                    }
                }
                $user = User::with('userDetails')->whereId($user->id)->firstOrFail();

                return [ 'data' => [ 'flags' =>  [ 'profile_percentage'    =>  $user->calculateProfilePercent(),],],
                        'meta'  => [
                            'message'       =>  trans('api.profile_setuped'), 
                            'auth_token'    =>  $user->createToken(config('utility.token'))->plainTextToken,
                        ]];
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\ProfileDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Profile details")]);
                        break;
                    case 'App\Models\UserInterest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User interest")]);
                        break;
                    case 'App\Models\UserDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User details")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'set_full_profile');
            }
        }

        return $this->returnResponse();  
    }

    public function generateChecksum(Request $request)
    {
        $rules = [
            'contact_no'    =>  'required',
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            try {
                $user = User::whereContactNo($request->contact_no)->first();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
                return response()->json([
                    'data'  =>  [
                        'checksum'  =>  NULL,
                        'data'      =>  $request->all(),
                        'message'   =>  'Contact details not found!'
                    ]
                ], 404);
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'generate_checksum');
            }

            $key = config('utility.checksum.key');
            $algo = config('utility.checksum.algorithm');

            $data = [
                'time'          =>  \Carbon\Carbon::now(),
                'contact_no'    =>  $user ? $user->contact_no : $request->contact_no,
            ];

            $data = json_encode($data);
            $iv = str_random(openssl_cipher_iv_length($algo));
            $value = \openssl_encrypt($data, $algo, $key, 0, $iv);
            $mac = hash_hmac(
                'sha256',
                base64_encode($iv) . $value,
                $key
            );
            $iv = base64_encode($iv);
            $payload = base64_encode(json_encode(compact('iv', 'value', 'mac')));

            /*
                    - Steps details
                        $pData = json_decode(base64_decode($payload), true );
                        $original = openssl_decrypt(
                                $pData['value'],
                                $algo,
                                $key,
                                0,
                                base64_decode($pData['iv'])
                            );

                        if( $request->view_steps == 'true'  ) {
                            return response()->json([
                                'key'   =>  [
                                    'encoded'   =>  $key,
                                    'actual'    =>  'Shared with you',
                                ],
                                'payload'       =>  [
                                    'data'      =>  $original,
                                    'encoded'   =>  $payload,
                                ],
                                'decoded'   =>  [
                                    'payload'   =>  $pData,
                                    'algo'      =>  $algo,
                                    'iv'        =>  base64_decode($pData['iv']),
                                ],
                                'flow' => [
                                    'step-1'    =>  'Use the \'key.encoded\' and perform base64_decode and check you get  \'key.actual\' or not',
                                    'step-2'    =>  [
                                        '1' =>  'perform base64_decode on \'payload.encoded\' and you get IV, VALUE and MAC.',
                                        '2' =>  'Now again perform base64_decode on IV you get after step 2.1',
                                        '3' =>  'Pass VALUD as it is, no need decoded.',
                                        '4' =>  'Apply algo on data you get, Pass encoded key when you decode the payload',
                                        '5' =>  'If you get your requested data from payload then you win (party)',
                                    ],
                                ],
                            ], 200);
                        }
                */

            return response()->json([
                'data'  =>  [
                    'checksum'  =>  $payload,
                    'data'      =>  $data,
                    'message'   =>  trans('Payload generated successfully')
                ]
            ], 200);
        }

        return $this->returnResponse();
    }

    // Customer Social Login
    public function socialLogin(Request $request)
    {
        $rules = SocialLoginRequest::rules($request);
        if( $this->apiValidator($request->all(), $rules, $this->version) ) {
            try {
                // Check for deleted account details
                $deleted = User::onlyTrashed()->pluck('email')->toArray();
                if( in_array($request->email, $deleted) ) {
                    $this->response['meta']['message']  =  trans('api.account_deleted');
                    $this->status = Response::HTTP_FORBIDDEN;
                    return $this->returnResponse();
                }

                $user = User::where('email', $request->email)->orWhere($request->type.'_id', $request[$request->type.'_id'])->first();            
                unset($request['type']);
                if( !empty($user) ) { # Update Profile Details
                    $request['full_name'] = $user->full_name ? $user->full_name : $request->full_name;
                    $user->fill($request->all());
                } else { # Create new user
                    $request['custom_id'] = getUniqueString('users');
                    $password = str_random(config('utility.password_length'));
                    $request['password'] = Hash::make($password);
                    
                    $user = User::create($request->all());
                    $user->is_social_user = 'y'; 
                }

                $path = $user->profile_photo;
                if( $request->has('profile_photo') ) {
                    if( $user->profile_photo ) if( Storage::exists($user->profile_photo) ) Storage::delete($user->profile_photo);
                    $path = $request->file('profile_photo')->store('users/profile_photo');
                }

                $user->profile_photo = $path;
                $user->save();
                $user = User::findOrFail($user->id);
                return (new UserProfile($user))
                    ->additional([
                    'meta' => [
                        'message'       =>  trans('api.login'), 
                        'auth_token'    =>  $user->createToken(config('utility.token'))->plainTextToken,
                    ] ]);
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'social_login');
            }
        }
        return $this->returnResponse();
    }
}
