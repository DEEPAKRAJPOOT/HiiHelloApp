<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Auth};
use App\Http\Requests\Api\Discovery\{SetDiscoveryRequest, SetDiscoveryLocationRequest};
use App\Http\Resources\v1\{DiscoveryResource};
use App\Models\{User, UserSetting, Location, Language, LocationTranslation};
use Illuminate\Support\Facades\Redis;

class DiscoveryController extends Controller
{
    private $version = "v.1.0";
    protected $redis;
    function __construct(Request $request,Redis $redis) {
        $this->redis = Redis::connection();
    }
    
    public function getVersion(){ return $this->version; }

    //Delete redis key by pattern
    public function deleteCacheByPattern($pattern)
    {
        $cursor = '0';
        do {
            list($cursor, $keys) = Redis::scan($cursor, ['match' => $pattern, 'count' => 100]);

            if (!empty($keys)) {
                
                $addedPrefix = str_replace('hi_hello_database_discover', 'discover', $keys[0]);
                $deleted = Redis::del($addedPrefix);
                
            }
        } while ($cursor != '0');

        return response()->json(['message' => 'Cache deleted successfully']);
    }

    public function setDiscoveryLocation(Request $request)
    {
        $setDiscoveryLocationRequest = new SetDiscoveryLocationRequest();
        if ($this->apiValidator($request->all(), $setDiscoveryLocationRequest->rules())) {
            try {
                $user = $request->user();
                $auth_id = $user->id;
                $pattern = 'hi_hello_database_discover/homefeed/getList/'.$auth_id.':*';
                $totaldiscoverkey = $auth_id.'_totallist:*';
               
                $this->deleteCacheByPattern($pattern);
                Redis::del($totaldiscoverkey);
                
                if($request->location =""){
                    $lat = $user->latitude;
                    $long = $user->longitude;

                    $location_id = $this->get_user_location($lat, $long);
                    $user->discover_location_id = $location_id;
                    $user->location_id = $location_id;
                    $user->save();
                }else{

                    $location = Location::select('id')->whereCustomId($request->location)->whereIsActive('y')->firstOrFail();
                    $user->discover_location_id = $location->id;
                    $user->save();
                }
                

                return ([
                    'data'  => NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.add', ['entity' => __('Location')]),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Location")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_discovery_location');
            }
        }
        return $this->returnResponse();
    }

    public function setDiscoveryDetail(Request $request)
    {
        $setDiscoveryRequest = new SetDiscoveryRequest();
        if ($this->apiValidator($request->all(), $setDiscoveryRequest->rules())) {
            try {
                $interest_trans_arr = [
                    'Both' => 'Both',
                    'Female' => 'Female',
                    'Male' => 'Male',
        
                    'দুয়োটা' => 'Both',
                    'পুৰুষ' => 'Male',
                    'মাইকী' => 'Female',
        
                    'উভয়' => 'Both',
                    'পুরুষ' => 'Male',
                    'মহিলা' => 'Female',
        
                    'બંને' => 'Both',
                    'પુરુષ' => 'Male',
                    'સ્ત્રી' => 'Female',
        
                    'दोनों' => 'Both',
                    'पुरुष' => 'Male',
                    'महिला' => 'Female',
        
                    'ಎರಡೂ' => 'Both',
                    'ಪುರುಷ' => 'Male',
                    'ಣ್ಣು' => 'Female',
        
                    'രണ്ടും' => 'Both',
                    'ആൺ' => 'Male',
                    'സ്ത്രീ' => 'Female',
        
                    'दोन्ही' => 'Both',
                    'पुरुष' => 'Male',
                    'स्त्री' => 'Female',
        
                    'ଉଭୟ' => 'Both',
                    'ପୁରୁଷ' => 'Male',
                    'ମହିଳା' => 'Female',
        
                    'ਦੋਵੇਂ' => 'Both',
                    'ਨਰ' => 'Male',
                    'ਔਰਤ' => 'Female',
        
                    'இரண்டும்' => 'Both',
                    'ஆண்' => 'Male',
                    'பெண்' => 'Female',
        
                    'రెండు' => 'Both',
                    'పురుషుడు' => 'Male',
                    'స్త్రీ' => 'Female'
                ];
                $user = $request->user();
                $auth_id = $user->id;
                $pattern = 'hi_hello_database_discover/homefeed/getList/'.$auth_id.':*';
                $totaldiscoverkey = $auth_id.'_totallist:*';
               
                $this->deleteCacheByPattern($pattern);
                Redis::del($totaldiscoverkey);

                if(empty($request->location)){
                    $lat = $user->latitude;
                    $long = $user->longitude;

                    $location_id = $this->get_user_location($lat, $long);
                    $user->discover_location_id = $location_id;
                    $user->location_id = $location_id;
                }else{
                    $location = Location::select('id')->whereCustomId($request->location)->whereIsActive('y')->firstOrFail();
                    $user->discover_location_id     =   $location->id;
                }
                
                $user->discover_distance        =   $request->distance;
                $user->discover_start_age       =   $request->start_age;
                $user->discover_end_age         =   $request->end_age;
                $user->interest                 =   $interest_trans_arr[$request->interest];
                $user->discover_profile_ranking =   $request->profile_ranking;
                $user->discover_has_photo      =    $request->has_photo;
                $user->discover_search_near_me =    $request->search_near_me;
                $user->discover_by_state       =    $request->search_by_state;
                $user->discover_state          =    $request->state;
                $user->discover_online_status  =    $request->online_status;
                $user->discover_relationship_status =  $request->relationship_status;
                $user->discover_education =   $request->education;
                $user->discover_verified_profile = $request->verified_profile;
                $user->save();

                if (!empty($request->languages)) {
                    $not_delete_interests = [];
                    $language_ids   =   Language::whereIn('lang_code', $request->languages)->whereIsActive('y')->pluck('id')->toArray();
                    foreach ($language_ids as $language_id) {
                        $custom_id = getUniqueString('user_settings');

                        UserSetting::updateOrCreate([
                            'user_id'       =>  $user->id,
                            'language_id'   =>  $language_id,
                        ], [
                            'custom_id'     =>  $custom_id,
                        ]);
                        $not_delete_interests[] = $custom_id;
                    }

                    // Delete Languages
                    UserSetting::whereUserId($user->id)->whereNotIn('custom_id', $not_delete_interests)->delete();
                }

                return ([
                    'data'  => NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.add', ['entity' => __('Discovery')]),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Location")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\Language':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Language")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'set_discovery');
            }
        }
        return $this->returnResponse();
    }

    public function getDiscoveryDetail(Request $request)
    {
        try {
            $user = User::select(
                'id',
                'custom_id',
                'interest',
                'discover_distance',
                'discover_start_age',
                'discover_end_age',
                'discover_location_id',
                'discover_profile_ranking',
                'discover_has_photo',
                'discover_search_near_me',
                'discover_by_state',
                'discover_state',
                'discover_online_status',
                'discover_relationship_status',
                'discover_education',
                'discover_verified_profile'
            )
                ->with([
                    'discoveryLocation:id,custom_id,is_active',
                    'discoveryLocation.locationTranslation:id,locale,location_id,name',
                    'userSettings:id,custom_id,user_id,language_id',
                    'userSettings.language:id,custom_id,language,lang_code,hint',
                ])
                ->whereId(Auth::id())->firstOrFail();

            return (new DiscoveryResource($user))
                ->additional([
                    'meta' => [
                        'message'       =>  trans('api.list', ['entity' => __("Discovery")]),
                        'is_ban'        =>  false,
                    ]
                ]);
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
            $this->storeErrorLog($e, 'get_discovery');
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
                if ($value->types[0] == "country") {
                    $result['country_long'] = trim($value->long_name);
                    $result['country_short'] = trim($value->short_name);
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
                        $LocationTranslation->country = $result['country_long'];
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

}
