<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\v1\UserProfile;
use App\Models\User;
use App\Models\Country;
use App\Models\UserDetail;

class AuthenticationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function login(Request $request)
    {
        $rules = [
            'contact_no'            =>  'required|min:2|max:16',
            'security_token'        =>  'required_with:contact_no|min:10|string',
        ];
        
        if( $this->apiValidator($request->all(), $rules) ) {
            $this->response['meta']['message']  = trans('api.login_fail');
            $this->status = $this->statusArr['forbidden'];

            $checksumDetails = $this->validateCheckSum($request->security_token, $request->contact_no);
            if( $checksumDetails->validate ) {
                try {
                    $user = User::whereContactNo($request->contact_no)->firstOrFail();
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
                } catch(\Exception $exception) {
                    $this->response['meta']['message']  = trans('api.login_fail');
                }
            } else {
                $this->response['meta']['message']  = $checksumDetails->message;
            }
        }
        return $this->returnResponse();
    }

    public function setProfile(Request $request)
    {
        $phone_codes = Country::whereIsActive('y')->pluck('phonecode')->toArray();
        $country_ids = Country::whereIsActive('y')->pluck('custom_id')->toArray();
        
        $rules = [
            'first_name'        =>  'required|min:2|max:100',
            'last_name'         =>  'required|min:2|max:100',
            'email'             =>  'nullable|email|max:150',
            'country_code'      =>  'required|in:'.implode(',', $phone_codes),
            'country'           =>  'nullable|in:'.implode(',', $country_ids),
            'contact_no'        =>  'required|digits_between:6,16',
            'birth_date'        =>  'required|date|before:tomorrow',
            'gender'            =>  'required|in:'.implode(',', ['Male','Female']),
            'interest'          =>  'required|in:'.implode(',', ['Male','Female', 'Both']),
            'profile_photo'     =>  'required|mimes:jpg,jpeg,png',
            'images'            =>  'required|array|max:4',
            'images.*'          =>  'required|mimes:jpg,jpeg,png',
            'videos'            =>  'nullable|array|max:1',
            'videos.*'          =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',
        ];

        if( $this->apiValidator($request->all(), $rules) ) {
            $country_id = NULL;
            if($request->has('country')){
                $country = Country::whereIsActive('y')->where('custom_id',$request->country)->first();
                if($country){ $country_id = $country->id; }
            }

            $user = User::updateOrCreate([
                'country_code'      =>  $request->country_code ?? NULL,
                'contact_no'        =>  $request->contact_no ?? NULL,
            ],[
                'custom_id'         =>  getUniqueString('users'),
                'first_name'        =>  $request->first_name ?? NULL,
                'last_name'         =>  $request->last_name ?? NULL,
                'email'             =>  $request->email ?? NULL,
                'birth_date'        =>  $request->birth_date ?? NULL,
                'gender'            =>  $request->gender ?? NULL,
                'interest'          =>  $request->interest ?? NULL,
                'country_id'        =>  $country_id ?? NULL,
                'password'          =>  Hash::make(config('utility.default_password')),
            ]);

            if( !empty($request->profile_photo) ) {
                if(!empty($user->profile_photo)){
                    if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                }
                $path = $request->file('profile_photo')->store('users/profile_photo');
                $user->profile_photo = $path;
            }

            if($user->save()){
                //Store Images
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
                
                //Store Video
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

                return (new UserProfile($user))
                    ->additional([
                        'meta' => [
                            'message' =>  trans('api.profile_setuped'), 
                        ]
                    ]);
            }else{
                $this->response['meta']['message']  = trans('api.profile_setuped_fail');
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
                    'message'   =>  'Payload generated successfully'
                ]
            ], 200);
        }

        return $this->returnResponse();
    }
    
}
