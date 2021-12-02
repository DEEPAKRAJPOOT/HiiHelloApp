<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\v1\UserProfile;
use App\Models\User;

class AuthenticationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function login(Request $request)
    {
        $rules = [
            'email'         =>  'required|min:2|max:100',
            'password'      =>  'required|string|min:8|max:16',
        ];

        if( $this->apiValidator($request->all(), $rules, $this->version) ) {
            $user = User::where('email',$request->email)->first();
            if($user){
                if($user->is_active == 'y'){
                    $attempt = ['email' => $request->email, 'password' => $request->password];
                    if( Auth::attempt($attempt) ){
                        $user = User::where('id',Auth::id())->firstOrFail();
                            
                        return (new UserProfile($user))
                                ->additional([
                                'meta' => [
                                    'message'           =>  trans('api.login'),
                                    'auth_token'        =>  $user->createToken(config('utility.token'))->plainTextToken,
                                ] ]);
                    }else {
                        $this->status = $this->statusArr['forbidden'];
                        $this->response['meta']['message']  = trans('api.login_fail');
                    }
                }else{
                    $this->status = $this->statusArr['forbidden'];
                    $this->response['meta']['message']  = trans('api.in_active');
                }
            }else{
                $this->status = $this->statusArr['forbidden'];
                $this->response['meta']['message']  = trans('api.not_found',['entity' => 'User details']);
            }
        }
        $this->response['meta']['api'] = $this->getVersion();
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }

    public function register(Request $request)
    {
        $rules = [
            'first_name'            =>  'required|max:100',
            'last_name'             =>  'required|max:100',
            'email'                 =>  'required|email|max:150|unique:users',
            'contact_no'            =>  'nullable|max:150|digits_between:6,16|unique:users',
            'password'              =>  'required|min:8|max:16',
            'confirm_password'      =>  'required|same:password|min:8|max:16',
        ];

        if( $this->apiValidator($request->all(), $rules, $this->version ) ) {
            $request['custom_id'] = getUniqueString('users');
            $request['password'] = Hash::make($request->password);   

            $user = User::create($request->all());            
            $user->save();

            $this->status = $this->statusArr['success'];
            return (new UserProfile($user))
                ->additional([
                'meta' => [
                    'message'       =>  trans('api.registered'),
                    'auth_token'    =>  $user->createToken(config('utility.token'))->plainTextToken,
                    ] ]);
        }
        $this->response['meta']['api']      =   $this->getVersion();
        $this->response['meta']['url']      =   url()->current();
        return $this->return_response();
    }
}
