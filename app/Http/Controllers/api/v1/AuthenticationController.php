<?php

namespace App\Http\Controllers\api\v1;

use DB;
use Illuminate\Support\Str;
use App\Classes\ImageDetectionClass;
use App\Http\Controllers\Controller;
use App\Http\Resources\OTPLessResource;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Storage, Auth, Hash};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Resources\v1\{UserProfile, LoginResource, SignUpResource};
use App\Http\Requests\Api\Authentication\{LoginRequest, OTPLessRequest, RegisterRequest, SocialLoginRequest};
use App\Models\{User, Country, UserDetail, Location, Interest, UserInterest, Language, ProfileDetail, DeviceToken, Subscription, SubscriptionPlan, LocationTranslation, ApiLogs, ImageModerationLog};

class AuthenticationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }
    public function getAuthUser()
    {
        return auth('sanctum')->user();
    }

    // User Login
    public function login(Request $request)
    {
        $loginRequest = new LoginRequest();
        if ($this->apiValidator($request->all(), $loginRequest->rules())) {
            $this->response['meta']['message']  = trans('api.login_fail');
            $this->status = Response::HTTP_FORBIDDEN;

            $checksumDetails = $this->validateCheckSum($request->security_token, $request->contact_no);
            if ($checksumDetails->validate) {
                try {
                    $user = User::with([
                        'userTranslation', 'userTransEn', 'userDetails', 'interests.interest.interestTranslation',
                        'language', 'location.locationTranslation'
                    ])
                        ->whereContactNo($request->contact_no)->firstOrFail();
                    if ($user->is_active == 'y') {
                        Auth::login($user);
                        Auth::user()->tokens()->delete();  // Logout From All Devices    
                        $user->changeLanguage(); // Change Language

                        return (new LoginResource($user))
                            ->additional([
                                'meta' => [
                                    'message'           =>  trans('api.login'),
                                    'auth_token'        =>  $user->createToken(config('utility.token'))->plainTextToken,
                                    'user_status'  =>  $user->user_status
                                ]
                            ]);
                    } else {
                        $this->response['meta']['message']  = trans('api.in_active');
                    }
                } catch (\Exception $e) {
                    $this->storeErrorLog($e, 'login', trans('api.login_fail'));
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
        $registerRequest = new RegisterRequest();
        if ($this->apiValidator($request->all(), $registerRequest->rules())) {
            try {

                $user = $this->getAuthUser();

                $country_id = $location_id = $language_id = $device_type = $device_app_version = NULL;
                $full_name = $request->first_name . ' ' . $request->last_name;
                if ($request->language == 'en') {
                    $full_name = Str::title($full_name);
                }
                $traslate_data = [];

                if (empty($user) && !empty($request->contact_no)) {
                    $user = User::whereContactNo($request->contact_no)->first();
                }
                if (!empty($request->country_code)) {
                    $country = Country::wherePhonecode($request->country_code)->whereIsActive('y')->firstOrFail();
                    $country_id = $country->id;
                }
                if (!empty($request->latitude) && !empty($request->longitude)) {
                    $locationdata = $this->get_user_location($request->latitude, $request->longitude);
                    $location_id = !empty($locationdata) ? $locationdata : NULL;
                    // $location_id = 1;
                    $new_location_id = 'y';
                } else {
                    $new_location_id = 'n';
                }
                if (!empty($request->language)) {
                    $language = Language::whereLangCode($request->language)->whereIsActive('y')->firstOrFail();
                    $language_id = $language->id;
                }
                if (!empty($request->device_type)) {
                    $device_type = $request->device_type;
                }
                if (!empty($request->device_app_version)) {
                    $device_app_version = $request->device_app_version;
                }
                if (empty($user) && !empty($request->email)) {
                    $user = User::whereEmail($request->email)->first();
                }
                if (!empty($request->email)) {
                    $is_social_user = 'y';
                } else {
                    $is_social_user = 'n';
                }
                // echo "<pre>"; print_r($request->all()); die();
                if (!empty($user)) {
                    $user->fill($request->all());
                    $user->country_id = $country_id;
                    $user->location_id = isset($location_id) ? $location_id : null;
                    $user->discover_location_id = isset($location_id) ? $location_id : null;
                    $user->language_id = $language_id;
                    $user->otp_less_id = $request->otp_less_id ?? null;
                } else {
                    // echo "<pre>"; print_r($request->all()); die();
                    $user = User::create([
                        'custom_id'             =>  getUniqueString('users'),
                        'account_id'            =>  Str::slug(substr($full_name, 0, 4), "_") . '_' . time(),
                        'birth_date'            =>  $request->birth_date ?? NULL,
                        'gender'                =>  $request->gender ?? NULL,
                        'interest'              =>  $request->interest ?? NULL,
                        'country_id'            =>  $country_id ?? NULL,
                        'country_code'          =>  $request->country_code ?? NULL,
                        'contact_no'            =>  $request->contact_no ?? NULL,
                        'email'                 =>  isset($request->email) ? $request->email : NULL,
                        'location_id'           =>  $location_id ?? NULL,
                        'discover_location_id'  =>  $location_id ?? NULL,
                        'new_location_id'       =>  $new_location_id,
                        'language_id'           =>  $language_id ?? NULL,
                        'is_social_user'        =>  isset($request->is_social_user) ? $request->is_social_user : $is_social_user,
                        'google_id'             =>  isset($request->google_id) ? $request->google_id : NULL,
                        'apple_id'              =>  isset($request->apple_id) ? $request->apple_id : NULL,
                        'facebook_id'           =>  isset($request->facebook_id) ? $request->facebook_id : NULL,
                        'device_type'           =>  $device_type ?? NULL,
                        'device_app_version'    =>  $device_app_version ?? NULL,
                        'star_sign_id'          =>  $request->star_sign_id ?? NULL,
                        'password'              =>  Hash::make(config('utility.default_password')),
                        'otp_less_id'           =>  $request->otp_less_id ?? null
                    ]);
                }
                // echo "<pre>"; print_r($user); die();

                if (!empty($full_name)) {
                    $language_codes = Language::pluck('lang_code')->toArray();
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['full_name' =>  $full_name];
                    }
                    $user->update($traslate_data);

                    // Store Account Id
                    if (!empty($request->language) && $request->language == 'en') {
                        $user->account_id = Str::slug(mb_substr($full_name, 0, 4), "_", null) . '_' . time();
                    }else{
                        $user->account_id = Str::slug(mb_substr($full_name, 0, 4), "_", null) . '_' . time();
                    }
                    $user->is_trans_full_name = 'n';
                }

                // Buy Subscription For Girls
                if ($user->wasRecentlyCreated || ($user->gender == 'Female' && empty($user->subscription))) {
                    $user->buyFreeSubscription();
                }

                if ($user->wasRecentlyCreated && !empty($user->country_code) && !empty($user->contact_no)) {
                    $user->contact_verified_at = \Carbon\Carbon::now();  // Set Contact Number As Verified
                    $user->sendWelcomeSms(); // Send Welcome SMS
                }


                $safe_image = "true";
                $awsImgResultArr = [];

                if ($request->hasFile('profile_photo')) {
                    if (!empty($user->profile_photo) && Storage::exists($user->profile_photo)) {
                        Storage::delete($user->profile_photo);
                    }
                    // $user->is_media_checked = 'n';
                    // $user->profile_photo = $request->file('profile_photo')->store('users/profile_photo');
                    // $user->save();

                    ///CHECK FOR AWS REKOGNIZTION START
                    $image_detection = new ImageDetectionClass($request->file('profile_photo'), $user);
                    $awsImgResultArr = $image_detection->checkConstraints();

                    $safe_image = $awsImgResultArr["is_safe_image"];
                    $user->profile_photo = null;
                    $user->is_media_checked = 'n';

                    if ($awsImgResultArr["is_safe_image"]) {
                        $user->profile_photo = $request->file('profile_photo')->store('users/profile_photo');
                        $user->save();
                    }

                    $message = $awsImgResultArr["log_message"];
                    $total_face_detected = $awsImgResultArr["total_face_detected"];
                    $response_data = $awsImgResultArr["image_moderation_response"];
                    $request_data = $awsImgResultArr["image_moderation_request"];
                    $endpoint_url = url()->current();

                    ImageModerationLog::Create([
                        'user_id'             => $user->id,
                        'is_approved'         => $awsImgResultArr["is_safe_image"] ? 1  : 0,
                        'request'             => $request_data,
                        'response'            => $response_data,
                        'total_face_detected' => $total_face_detected,
                        'message'             => $message,
                        'image_type'          => "profile_photo",
                        'endpoint_url'        => $endpoint_url,
                    ]);
                    //CHECK FOR AWS REKOGNIZTION END
                }
                // if (!empty($request->profile_photo)) {

                //     if (!empty($user->profile_photo)) {
                //         if (Storage::exists($user->profile_photo)) {
                //             Storage::delete($user->profile_photo);
                //         }
                //     }
                //     ///CHECK FOR AWS REKOGNIZTION START
                //     $awsImgResultArr = checkAwsImageModeration($request, "profile_photo");

                //     if (count($awsImgResultArr) > 0) {
                //         if ($awsImgResultArr["is_safe_image"] == true) {
                //             $path = $request->file('profile_photo')->store('users/profile_photo');
                //             $user->profile_photo = $path;
                //             $user->is_media_checked = 'n';
                //         } else {
                //             $user->profile_photo = NULL;
                //             $user->is_media_checked = 'n';
                //             $invalid_image_uploaded = true;
                //             $safe_image = "false";
                //         }

                //         //INSERT IN TO IMAGE MODERATIO LOG START
                //         if ($awsImgResultArr["is_safe_image"] == true)
                //             $is_approved = 1;
                //         else
                //             $is_approved = 0;

                //         $image_type = "profile_photo";
                //         $message = $awsImgResultArr["log_message"];
                //         $total_face_detected = $awsImgResultArr["total_face_detected"];

                //         $response_data = $awsImgResultArr["image_moderation_response"];
                //         $request_data = $awsImgResultArr["image_moderation_request"];


                //         $endpoint_url = url()->current();


                //         ImageModerationLog::Create([
                //             'user_id'             => $user->id,
                //             'is_approved'         => $is_approved,
                //             'request'             => $request_data,
                //             'response'            => $response_data,
                //             'total_face_detected' => $total_face_detected,
                //             'message'             => $message,
                //             'image_type'          => $image_type,
                //             'endpoint_url'        => $endpoint_url,
                //         ]);

                //         //INSERT IN TO IMAGE MODERATIO LOG END

                //     }
                //     //CHECK FOR AWS REKOGNIZTION END
                // }

                $user->latitude = $request->latitude;
                $user->longitude = $request->longitude;
                $user->setprofile_api_run = 'y';

                // Set Default Discover
                $user->discover_distance    =   config('utility.profile.detail.discover_distance');
                $user->discover_start_age   =   config('utility.profile.detail.discover_start_age');
                $user->discover_end_age     =   config('utility.profile.detail.discover_end_age');

                if ($user->save()) {
                    $user = User::with(['userTranslation', 'interests', 'userDetails', 'location.locationTranslation', 'language'])
                        ->whereId($user->id)->firstOrFail();
                    Auth::login($user);

                    // store api request and responce
                    $apilogs = new ApiLogs();
                    $apilogs->user_id = $user->id;
                    $apilogs->url = url()->current();
                    $apilogs->request = json_encode($request->all());
                    $apilogs->response = json_encode(new SignUpResource($user));
                    $apilogs->api_status = 200;
                    $apilogs->save();

                    return (new SignUpResource($user))
                        ->additional([
                            'meta' => [
                                'message'       =>  trans('api.profile_setuped'),
                                'auth_token'    =>  $user->createToken(config('utility.token'))->plainTextToken,
                                'safe_image'    =>  $safe_image,
                                'image_description' => $awsImgResultArr
                            ]
                        ]);
                } else {
                    $this->response['meta']['message']   = trans('api.profile_setuped_fail');
                    $this->response['meta']['safe_image'] = $safe_image;
                }
            } catch (ModelNotFoundException $exception) {
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
                    case 'App\Models\Language':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Language")]);
                        break;
                    case 'App\Models\UserDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_profile');
            }
        }

        return $this->returnResponse();
    }

    // Generate Checksum For Login
    public function generateChecksum(Request $request)
    {
        $rules = [
            'contact_no'    =>  'required',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
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
                $this->storeErrorLog($e, 'generate_checksum');
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
        $socialLoginRequest = new SocialLoginRequest();
        if ($this->apiValidator($request->all(), $socialLoginRequest->rules($request), $this->version)) {
            try {
                // Check for deleted account details
                $account_del = false;
                $traslate_data = [];

                /* if (!empty($request->email)) {
                    $deleted = User::onlyTrashed()->pluck('email')->toArray();
                    if (in_array($request->email, $deleted)) {
                        $account_del = true;
                    }
                } else {
                    $deleted = User::onlyTrashed()->where($request->type . '_id', $request[$request->type . '_id'])->first();
                    if ($deleted) {
                        $account_del = true;
                    }
                }

                if ($account_del) {
                    $this->response['meta']['message']  =  trans('api.account_deleted');
                    $this->status = Response::HTTP_FORBIDDEN;
                    return $this->returnResponse();
                } */

                $user = User::query();
                if (!empty($request->type)) {
                    $user = $user->where($request->type . '_id', $request[$request->type . '_id']);
                } else {
                    $user = $user->where('email', $request->email);
                }
                $user = $user->first();

                unset($request['type']);
                if (!empty($user)) { # Update Profile Details
                    // $request['full_name'] = $user->full_name ? $user->full_name : $request->full_name;
                    $user->fill($request->all());
                } else { # Create new user
                    $request['custom_id'] = getUniqueString('users');
                    $password = str_random(config('utility.password_length'));
                    $request['password'] = Hash::make($password);

                    $user = User::create($request->all());
                    $user->is_social_user = 'y';
                }

                if (!empty($request->full_name) && empty($user->full_name)) {
                    $language_codes = Language::pluck('lang_code')->toArray();
                    foreach ($language_codes as $language_code) {
                        $traslate_data[$language_code] =  ['full_name' =>  $request->full_name];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_full_name = 'n';
                }

                $path = $user->profile_photo;
                if ($request->has('profile_photo')) {
                    if ($user->profile_photo) if (Storage::exists($user->profile_photo)) Storage::delete($user->profile_photo);
                    $path = $request->file('profile_photo')->store('users/profile_photo');
                }

                if (empty($user->email_verified_at)) {
                    $user->markEmailAsVerified();
                } // Mark Email As Verified
                if ($user->wasRecentlyCreated) {
                    $user->buyFreeSubscription();
                } // Buy Subscription For Girls

                $user->profile_photo = $path;
                $user->setprofile_api_run = 'n';
                $user->save();

                return (new UserProfile($user))
                    ->additional([
                        'meta' => [
                            'message'       =>  trans('api.login'),
                            'auth_token'    =>  $user->createToken(config('utility.token'))->plainTextToken,
                        ]
                    ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'social_login');
            }
        }
        return $this->returnResponse();
    }

    // User Logout
    public function logout()
    {
        try {
            $user = User::whereId(Auth::id())->firstOrFail();
            DeviceToken::whereUserId($user->id)->delete();  // Device Token Delete
            auth()->user()->tokens()->delete();  // Auth Token Revoke

            $this->status = Response::HTTP_OK;
            $this->response['meta']['message'] = trans('api.logout');
            $this->response['meta']['is_ban'] = false;
        } catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                    $this->response['meta']['is_ban'] = false;
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    $this->response['meta']['is_ban'] = false;
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e, 'logout');
        }
        return $this->returnResponse();
    }

    // User get location id using lat and logn
    public function get_user_location($lat, $long)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $latlng = $lat . ',' . $long;
        $result = [];
        $location_id = '';

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlng . "&sensor=true&key=" . $apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $responseJson = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responseJson);
        // echo "<pre>"; print_r($response); die();
        if (!empty($response) && !empty($response->results[0]->address_components)) {
            $location = false;
            foreach ($response->results[0]->address_components as $key => $value) {
                if ($value->types[0] == "administrative_area_level_3") {
                    $location = true;
                    $result['city'] = trim($value->long_name);
                }
                if ($value->types[0] == "administrative_area_level_1") {
                    $result['state'] = trim($value->long_name);
                }

                // check city and state not empty
                if (!empty($result) && !empty($result['city']) && !empty($result['state'])) {
                    // if already exist city and state then get id and update user location id
                    $city = strtok($result['city'], " ");
                    $locationTranslation = LocationTranslation::join('locations', 'locations.id', '=', 'location_translations.location_id')->where('locations.is_active','=','y')->where('name','LIKE',"%{$city}%")->where('state', $result['state'])->where('locale', 'en')->first(); 
                    
                    if (!empty($locationTranslation)) {
                        $location_id = $locationTranslation->location_id;

                        // update location table for city is used some one users
                        Location::where('id', $location_id)->update([
                            'is_used' =>  'y',
                        ]);
                    } else {
                        // if city and state not exits then create new
                        $result['city'] = $city;
                        $location = new Location();
                        $location->custom_id = getUniqueString('locations');
                        $location->is_used   = 'y';
                        $location->save();

                        $location_id = $location->id;

                        $LocationTranslation = new LocationTranslation();
                        $LocationTranslation->locale = 'en';
                        $LocationTranslation->location_id = $location_id;
                        $LocationTranslation->name = $result['city'];
                        $LocationTranslation->state = $result['state'];
                        $LocationTranslation->save();
                    }
                }
            }

            if($location == false){
                
                if (!empty($response)) {
                     
                     foreach($response->results as $res){
                        if(isset($res->address_components) && !empty($res->address_components)){
                            
                            foreach ($res->address_components as $key => $value) {
                                if ($value->types[0] == "administrative_area_level_3") {
                                    $location = true;
                                    $result['city'] = trim($value->long_name);
                                }

                                if ($value->types[0] == "administrative_area_level_1") {
                                    $result['state'] = trim($value->long_name);
                                }

                                // check city and state not empty
                                if (!empty($result) && !empty($result['city']) && !empty($result['state'])) {
                                    // if already exist city and state then get id and update user location id
                                    
                                    $city = strtok($result['city'], " ");
                                    $locationTranslation = LocationTranslation::join('locations', 'locations.id', '=', 'location_translations.location_id')->where('locations.is_active','=','y')->where('name','LIKE',"%{$city}%")->where('state', $result['state'])->where('locale', 'en')->first(); 
                                    
                                    if (!empty($locationTranslation)) {
                                        $location_id = $locationTranslation->location_id;

                                        // update location table for city is used some one users
                                        Location::where('id', $location_id)->update([
                                            'is_used' =>  'y',
                                        ]);
                                    } else {
                                        // if city and state not exits then create new
                                        $result['city'] = $city;
                                        $location = new Location();
                                        $location->custom_id = getUniqueString('locations');
                                        $location->is_used   = 'y';
                                        $location->save();

                                        $location_id = $location->id;

                                        $LocationTranslation = new LocationTranslation();
                                        $LocationTranslation->locale = 'en';
                                        $LocationTranslation->location_id = $location_id;
                                        $LocationTranslation->name = $result['city'];
                                        $LocationTranslation->state = $result['state'];
                                        $LocationTranslation->save();
                                    }
                                }
                            }
                        }
                     }
                }
            }
            return $location_id;
        }
    }

    public function otpLessLogin(Request $request)
    {
        $otpLessRequest = new OTPLessRequest();
        if ($this->apiValidator($request->all(), $otpLessRequest->rules($request), $this->version)) {
            try {


                $this->response['meta']['message']  = trans('api.login_fail');

                $verifiedOTPLessAuth = verifyOTPLessAuth($request->wa_id);
                if (is_array($verifiedOTPLessAuth)) {
                    $verifiedOTPLessAuth['otp_less_id'] = $request->wa_id;

                    $this->status = Response::HTTP_UNPROCESSABLE_ENTITY;
                    $this->response['meta']['otp_less_data']  = $verifiedOTPLessAuth;
                    try {
                        $user = User::with([
                            'userTranslation', 'userTransEn', 'userDetails', 'interests.interest.interestTranslation',
                            'language', 'location.locationTranslation'
                        ])
                            ->whereContactNo(substr($verifiedOTPLessAuth['userMobile'], 2))->firstOrFail();
                        if ($user->is_active == 'y') {
                            Auth::login($user);
                            Auth::user()->tokens()->delete(); // Logout From All Devices    
                            $user->changeLanguage(); // Change Language
                            $user->otp_less_id = $request->wa_id;
                            $user->save();
                            return (new LoginResource($user))
                                ->additional([
                                    'meta' => [
                                        'message'           =>  trans('api.login'),
                                        'auth_token'        =>  $user->createToken(config('utility.token'))->plainTextToken,
                                        'otp_less_data'     => $verifiedOTPLessAuth
                                    ]
                                ]);
                        } else {
                            $this->response['meta']['message']  = trans('api.in_active');
                        }
                    } catch (\Exception $e) {
                        $this->storeErrorLog($e, 'login', trans('api.login_fail'));
                    }
                } else {
                    $this->status = Response::HTTP_UNPROCESSABLE_ENTITY;
                    $this->response['meta']['message']  = $verifiedOTPLessAuth;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'otp_less_login');
            }
        }
        return $this->returnResponse();
    }
}
