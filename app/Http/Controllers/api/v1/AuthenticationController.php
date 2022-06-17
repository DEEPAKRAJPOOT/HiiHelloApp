<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Resources\v1\ { UserProfile, LoginResource, SignUpResource };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Storage, Auth, Hash };
use App\Http\Requests\Api\Authentication\ { LoginRequest, RegisterRequest, SocialLoginRequest };
use App\Models\ { User, Country, UserDetail, Location, Interest, UserInterest, Language, ProfileDetail, DeviceToken };
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }
    public function getAuthUser(){ return auth('sanctum')->user(); }

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
                    $user = User::with(['userTranslation','userDetails','interests.interest.interestTranslation',
                                    'language','location.locationTranslation'])
                                        ->whereContactNo($request->contact_no)->withCount('likes')->firstOrFail();
                    if($user->is_active == 'y'){
                        Auth::login($user);
                        Auth::user()->tokens()->delete(); // Logout From All Devices    

                        return (new LoginResource($user))
                            ->additional([
                                'data' => [ 'flags' =>  [
                                    'matches'   =>  $user->countMatches(), 'chats'  =>  $user->countChats(),
                                ] ], 
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
                $user = $this->getAuthUser();
                $country_id = $location_id = $language_id = NULL;
                $traslate_data = [];

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
                if(empty($user) && !empty($request->email)){
                    $user = User::whereEmail($request->email)->first();
                }

                if(!empty($user)){
                    $user->fill($request->all());
                    $user->country_id = $country_id;
                    $user->location_id = $location_id;
                    $user->discover_location_id = $location_id;
                    $user->language_id = $language_id;
                }else{
                    $user = User::updateOrCreate([
                        'country_code'          =>  $request->country_code ?? NULL,
                        'contact_no'            =>  $request->contact_no ?? NULL,
                    ],[
                        'custom_id'             =>  getUniqueString('users'),
                        // 'full_name'             =>  $request->full_name ?? NULL,
                        'birth_date'            =>  $request->birth_date ?? NULL,
                        'gender'                =>  $request->gender ?? NULL,
                        'interest'              =>  $request->interest ?? NULL,
                        'country_id'            =>  $country_id ?? NULL,
                        'location_id'           =>  $location_id ?? NULL,
                        'discover_location_id'  =>  $location_id ?? NULL,
                        'language_id'           =>  $language_id ?? NULL,
                        'password'              =>  Hash::make(config('utility.default_password')),
                    ]);
                }

                if(!empty($request->full_name)){
                    $language_codes = Language::pluck('lang_code')->toArray();
                    foreach($language_codes as $language_code){
                        $traslate_data[$language_code] =  [ 'full_name' =>  $request->full_name ];
                    }
                    $user->update($traslate_data);

                    // Store Account Id
                    if(!empty($request->language) && $request->language == 'en'){
                        $user->account_id = Str::slug(substr($request->full_name, 0, 4), "_").'_'.time();
                    }

                    $user->is_trans_full_name = 'n';
                }
                
                // Set Contact Number As Verified
                if($user->wasRecentlyCreated && !empty($user->contact_no)){
                    $user->contact_verified_at = \Carbon\Carbon::now(); 
                }

                if( !empty($request->profile_photo) ) {
                    if(!empty($user->profile_photo)){
                        if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                    }
                    $path = $request->file('profile_photo')->store('users/profile_photo');
                    $user->profile_photo = $path;
                    $user->is_media_checked = 'n';
                }

                // Set Default Discover
                $user->discover_distance    = config('utility.profile.detail.discover_distance');
                $user->discover_start_age   = config('utility.profile.detail.discover_start_age');
                $user->discover_end_age     = config('utility.profile.detail.discover_end_age');

                if($user->save()){
                    $user = User::with(['userTranslation','interests','userDetails','location.locationTranslation','language'])
                                    ->whereId($user->id)->firstOrFail();
                    Auth::login($user);
                    return (new SignUpResource($user))
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

    // Generate Checksum For Login
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
                $account_del = false;
                $traslate_data = [];

                if(!empty($request->email)){
                    $deleted = User::onlyTrashed()->pluck('email')->toArray();
                    if( in_array($request->email, $deleted) ) { $account_del = true; }
                }else{
                    $deleted = User::onlyTrashed()->where($request->type.'_id', $request[$request->type.'_id'])->first();
                    if($deleted){ $account_del = true; }
                }

                if($account_del){
                    $this->response['meta']['message']  =  trans('api.account_deleted');
                    $this->status = Response::HTTP_FORBIDDEN;
                    return $this->returnResponse();
                }

                $user = User::query();
                if(!empty($request->email)){
                    $user = $user->where('email', $request->email);
                }else{
                    $user = $user->where($request->type.'_id', $request[$request->type.'_id']);
                }
                $user = $user->first();
                  
                unset($request['type']);
                if( !empty($user) ) { # Update Profile Details
                    // $request['full_name'] = $user->full_name ? $user->full_name : $request->full_name;
                    $user->fill($request->all());
                } else { # Create new user
                    $request['custom_id'] = getUniqueString('users');
                    $password = str_random(config('utility.password_length'));
                    $request['password'] = Hash::make($password);
                    
                    $user = User::create($request->all());
                    $user->is_social_user = 'y'; 
                }

                if(!empty($request->full_name) && empty($user->full_name)){
                    $language_codes = Language::pluck('lang_code')->toArray();
                    foreach($language_codes as $language_code){
                        $traslate_data[$language_code] =  [ 'full_name' =>  $request->full_name ];
                    }
                    $user->update($traslate_data);
                    $user->is_trans_full_name = 'n';
                }

                $path = $user->profile_photo;
                if( $request->has('profile_photo') ) {
                    if( $user->profile_photo ) if( Storage::exists($user->profile_photo) ) Storage::delete($user->profile_photo);
                    $path = $request->file('profile_photo')->store('users/profile_photo');
                }

                $user->profile_photo = $path;
                $user->save();
                $user = User::withCount('likes')->findOrFail($user->id);
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

    // User Logout
    public function logout()
    {
        try {
            $user = User::whereId(Auth::id())->firstOrFail();
            // Device Token Delets
            DeviceToken::whereUserId($user->id)->delete();

            // Auth Token Revoke
            auth()->user()->tokens()->delete();
            
            $this->status = Response::HTTP_OK;
            $this->response['meta']['message'] = trans('api.logout');
        } catch(ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e,'logout');
        }
        return $this->returnResponse();
    }
}
