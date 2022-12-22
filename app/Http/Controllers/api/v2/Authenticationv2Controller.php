<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use App\Http\Resources\v2\{UserProfile, LoginResource, SignUpResource};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Storage, Auth, Hash};
use App\Http\Requests\Api\Authentication\{LoginRequest, RegisterRequest, SocialLoginRequest};
use App\Models\{User, Country, UserDetail, Location, Interest, UserInterest, Language, ProfileDetail, DeviceToken, Subscription, SubscriptionPlan};
use Illuminate\Support\Str;

class Authenticationv2Controller extends Controller
{
    private $version = "v.2.0";
    public function getVersion(){ return $this->version; }
    public function getAuthUser(){ return auth('sanctum')->user(); }

    // Customer Social Login
    public function social_login(Request $request)
    {
        $socialLoginRequest = new SocialLoginRequest();
        if ($this->apiValidator($request->all(), $socialLoginRequest->rules($request), $this->version)) {
            try {
                $profile_setuped = false;
                $token = "";
                $user = User::where('email',$request->email)->first();
                if (!empty($user)) {
                    $profile_setuped = true;
                    $token = $user->createToken(config('utility.token'))->plainTextToken;
                    // Check for deleted account details
                    $account_del = false;
                    $traslate_data = [];

                    return (new UserProfile($user))
                    ->additional([
                        'meta' => [
                            'message'       =>  trans('api.login'),
                            'auth_token'    =>  $token,
                        ]
                    ]);
                }
                else
                {
                    $this->response['profile_setuped'] = false;
                    $this->response['meta']['api'] = 'v.2.0';
                    $this->response['meta']['url'] = url()->current();
                    $this->response['meta']['language'] = app()->getLocale();
                    $this->response['meta']['message'] = 'User not found';
                    $this->response['meta']['auth_token'] = '';
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
                $this->storeErrorLog($e, 'social_login');
            }
        }
        return $this->returnResponse();
        
    }

}
