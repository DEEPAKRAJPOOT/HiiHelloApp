<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Storage, DB, Auth};
use App\Http\Resources\v1\{UserProfile, UserDetailResource, MyProfile};
use App\Http\Requests\Api\User\{ProfileRequest, ProfileReportRequest, SetLatLongRequest};
use App\Http\Requests\Api\Authentication\{DeleteAccountRequest};
use App\Http\Requests\Api\General\{PaginationRequest};
use App\Models\{User, Location, ProfileReport, NotificationStatus, Language,LocationTranslation};

class UserController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get User Profile
    public function getProfile(Request $request)
    {
        $profileRequest = new ProfileRequest();
        if( $this->apiValidator($request->all(), $profileRequest->rules()) ) {
            try{
                $auth_id = $request->user() ? $request->user()->id : NULL;
                // $max_interest = config('utility.profile.detail.max_interest') ?? 5;

                $user = User::select('id','custom_id','birth_date','location_id',
                            'profile_photo','voice','voice_answer', 'swipe_count', 'gender', 'verify_status',
                            'subscription_end_date',
                            'education_id','university_id','profession_id','religion_id',
                            'relationship_status_id','you_are_here_id','food_preference_id','drinking_id','smoking_id',
                            'pet_id','star_sign_id','community_id','is_active')
                        ->with([
                            // 'interests' => function($query) use ($max_interest) {
                            //     $query->latest()->take($max_interest); 
                            // },
                            'interests',
                            'userTranslation','userDetails','interests.interest.interestTranslation',
                            'interests.interest.parentInterest','interests.interest.masterInterest',
                            'location.locationTranslation',
                            'education.profileDetailTranslation','university.profileDetailTranslation',
                            'profession.profileDetailTranslation','religion.profileDetailTranslation',
                            'relationshipStatus.profileDetailTranslation','youAreHere.profileDetailTranslation',
                            'foodPreference.profileDetailTranslation','drinking.profileDetailTranslation',
                            'smoking.profileDetailTranslation','pet.profileDetailTranslation',
                            'starSign.profileDetailTranslation','community.profileDetailTranslation',
                            'personalities.personality.personalityTranslation'
                        ])
                        ->withCount(['blockedTos' => function ($query) use ($auth_id) {
                            $query->whereBlockBy($auth_id);
                        }])
                        ->whereCustomId($request->id)->whereIsActive('y')->firstOrFail();

                return (new UserDetailResource($user))
                            ->additional([
                            'meta' => [
                                'message'  =>  trans('api.success', ['entity' => __("User")]),
                                'is_ban'    =>  false,
                            ] ]);
            } catch(ModelNotFoundException $exception) {                
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
                $this->storeErrorLog($e,'get_profile');
            }
        }
        return $this->returnResponse();
    }

    // Store Profile Report Details
    public function storeProfileReport(Request $request)
    {
        $profileReportRequest = new ProfileReportRequest();
        if( $this->apiValidator($request->all(), $profileReportRequest->rules()) ) {
            try{
                $path = NULL;
                $reported_user = User::whereCustomId($request->reported_user)
                                    ->where('id','!=',Auth::id())
                                    ->whereIsActive('y')->firstOrFail();
                if(!empty($request->image)){
                    $path = $request->file('image')->store('profile_report');
                }

                $profile_report = ProfileReport::firstOrCreate([
                    'user_id'           =>  Auth::id(),
                    'reported_user_id'  =>  $reported_user->id,
                    'message'           =>  $request->message,
                ],[
                    'custom_id'         =>  getUniqueString('profile_reports'),
                    'image'             =>  $path,
                ]);

                if($profile_report->save()){
                    $this->status = Response::HTTP_OK;     
                    return response()->json([
                        'data'  =>  NULL,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.report.success'),
                    ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.report.fail'); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
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
                $this->storeErrorLog($e,'store_profile_report');
            }
        }
        return $this->returnResponse();
    }

    // Get Common Age Of All Users
    public function getCommonAge()
    {
        try{
            $common_age = User::select(DB::raw('MAX(birth_date) as min_date'), DB::raw('MIN(birth_date) as max_date'))
                            ->whereIsActive('y')->first();
            if(!empty($common_age)){
                return ([
                    'data'  =>  [
                        'max_date'  =>  $common_age->max_date ?? NULL,
                        'min_date'  =>  $common_age->min_date ?? NULL,
                    ],
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.list', ['entity' => __('Users Age')]),
                    ] ]);
            }else{
                $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Users Age')]); 
                $this->status = Response::HTTP_NOT_FOUND;     
            }
        } catch(ModelNotFoundException $exception) {                
            switch ($exception->getModel()) {
                case 'App\Models\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e,'get_common_age');
        }
        return $this->returnResponse();
    }

    // My Profile Details
    public function getMyProfile(Request $request)
    {
        try{
            $user = User::with([
                    'userTranslation','userTransEn','userDetails',
                    'subscription.subscriptionPlan.subscriptionPlanTranslation',
                    'subscription.subscriptionPlan.subscriptionPlanTransEn',
                    'interests.interest.interestTranslation',
                    'interests.interest.parentInterest','interests.interest.masterInterest',
                    'location.locationTranslation','language','education.profileDetailTranslation',
                    'university.profileDetailTranslation','profession.profileDetailTranslation',
                    'religion.profileDetailTranslation',
                    'relationshipStatus.profileDetailTranslation','youAreHere.profileDetailTranslation',
                    'foodPreference.profileDetailTranslation','drinking.profileDetailTranslation',
                    'smoking.profileDetailTranslation','pet.profileDetailTranslation',
                    'starSign.profileDetailTranslation','community.profileDetailTranslation',
                    'personalities.personality.personalityTranslation'
                    ])
                    ->whereId(Auth::id())->firstOrFail();

            return (new MyProfile($user))
                ->additional(['meta' => [
                    'message'   =>  trans('api.success', ['entity' => __("Profile")]),
                    'is_ban'    =>  false,
                ] ]);
        } catch(ModelNotFoundException $exception) {                
            switch ($exception->getModel()) {
                case 'App\Models\User':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                    $this->response['meta']['is_ban'] = false;
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    $this->response['meta']['is_ban'] = false;
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e,'my_profile');
        }
        return $this->returnResponse();
    }

    // Delete Account
    public function deletAccount(Request $request)
    {
        $deleteAccountRequest = new DeleteAccountRequest();
        if( $this->apiValidator($request->all(), $deleteAccountRequest->rules()) ) {
            try{
                $user = $request->user();
                $user->reason_of_delete = $request->reason;
                $user->app_delete = 'y';
                $user->save(); 
                $user->delete();
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.delete', ['entity' => __('Your Account')]),
                    ] ]);
            } catch(ModelNotFoundException $exception) {                
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
                $this->storeErrorLog($e,'delete_account');
            }
        }
        return $this->returnResponse();
    }

    // Store Latitude & Longitude Of User
    public function storeLatLong(Request $request)
    {
        $setLatLongRequest = new SetLatLongRequest();
        if( $this->apiValidator($request->all(), $setLatLongRequest->rules()) ) {
            try{
                $user = $request->user();

                //if db lat and long eampty and new lat and long not empty then lat and log to update location id user 09-01-23
                if (empty($user->latitude) && empty($user->longitude) && !empty($request->latitude) && !empty($request->longitude)) {
                    $location_id = $this->get_user_location($request->latitude,$request->longitude);
                    if (!empty($location_id)) {
                        $user->location_id = $location_id;
                        $user->discover_location_id = $location_id;
                        $user->new_location_id = 'y';
                        $user->latitude = $request->latitude;
                        $user->longitude = $request->longitude;
                    }
                }
                $user->current_latitude = $request->latitude;
                $user->current_longitude = $request->longitude;
                $user->save(); 
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.add', ['entity' => __('Current location')]),
                    ] ]);
            } catch(ModelNotFoundException $exception) {                
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
                $this->storeErrorLog($e,'store_latlong');
            }
        }
        return $this->returnResponse();
    }

    // Update Notification Status Of User
    public function readNotifications(Request $request)
    {
        try {
            $user = $request->user();
            $user->chat_count = 0;
            $user->save();
            NotificationStatus::whereUserId($user->id)->update(['is_read' => 'y']);
            
            $this->status = Response::HTTP_OK;
            $this->response['meta']['message']  =   trans('api.update',['entity' => __("Notification")]); 
            $this->response['meta']['is_ban'] = false;
        } catch(ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\NotificationStatus':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Notification")]);
                    $this->response['meta']['is_ban'] = false;
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    $this->response['meta']['is_ban'] = false;
                    break;
            };
        }
        return $this->returnResponse();
    }

    // User get location id using lat and logn
    public function get_user_location($lat,$long)
    {
        $apiKey = 'AIzaSyDInVSLHXa1FXO3p7kgA7B_TK9L71tZbW8';
        $latlng = $lat.','.$long;
        $result = [];
        $location_id = '';

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latlng."&sensor=true&key=".$apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
        $responseJson = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responseJson);
        if (!empty($response) && !empty($response->results[0]->address_components)) {
            foreach ($response->results[0]->address_components as $key => $value) {
                if ($value->types[0] == "administrative_area_level_3") {
                    $result['city'] = trim($value->long_name);
                }
                if ($value->types[0] == "administrative_area_level_1") {
                    $result['state'] = trim($value->long_name);
                }

                // check city and state not empty
                if (!empty($result) && !empty($result['city']) && !empty($result['state'])) {
                    // if already exist city and state then get id and update user location id
                    $locationTranslation = LocationTranslation::where('name',$result['city'])->where('state',$result['state'])->where('locale','en')->first();
                    if (!empty($locationTranslation)) {
                        $location_id = $locationTranslation->location_id;

                        // update location table for city is used some one users
                        Location::where('id',$location_id)->update([ 
                            'is_used' =>  'y',
                        ]);
                    }
                    else
                    {
                        // if city and state not exits then create new
                        $location = new Location();        
                        $location->custom_id = getUniqueString('locations');  
                        $location->is_used   = 'y';  
                        $location->is_new    = 'y';  
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
            return $location_id;
        }
    }
}
