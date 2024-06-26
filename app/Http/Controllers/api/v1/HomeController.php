<?php

namespace App\Http\Controllers\api\v1;

use Carbon\Carbon;
use App\Models\Like;
use App\Models\ProfileReport;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Support\Facades\{DB};
use Illuminate\Http\{Request, Response};
use App\Http\Resources\v1\{HomeResource};
use App\Http\Requests\Api\General\{PaginationRequest};
use App\Models\{User, BlockUser, UserSetting, DisLike,LocationTranslation};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    private $version = "v.1.0";
    protected $redis;

    function __construct(Request $request,Redis $redis) {
        $this->redis = Redis::connection();
    }

    public function getVersion()
    {
        return $this->version;
    }

    //Delete redis key by pattern
    public function deleteCacheByPattern($pattern)
    {
        $cursor = '0';
        do {
            list($cursor, $keys) = Redis::scan($cursor, ['match' => $pattern, 'count' => 100]);

            if (!empty($keys)) {
                
                $addedPrefix = str_replace('hi_hello_database_chat', 'chat', $keys[0]);
                $deleted = Redis::del($addedPrefix);
                
            }
        } while ($cursor != '0');

        return response()->json(['message' => 'Cache deleted successfully']);
    }

    // Get All Users List
    public function getHomeFeeds(Request $request)
    {
        $paginationRequest = new PaginationRequest();
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
            try {
                DB::enableQueryLog();
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;
                $is_swipe_allow = $user->isSwipeAllow();
                
                if ($is_swipe_allow) {
                    $limit = $request->limit;
                    $offset = $request->offset;
                    $paginate = (int)$limit+(int)$offset;
                    $prevDataSetKey = 'discover/homefeed/getList/'.$auth_id.':'.$auth_id.'_getlist_'.$offset;
                    $getPrevDataSet = $this->redis->get($prevDataSetKey);
                    if(!is_null($getPrevDataSet)){
                        $this->redis->del($prevDataSetKey);
                    }
                    $homefeedskey = 'discover/homefeed/getList/'.$auth_id.':'.$auth_id.'_getlist_'.$paginate;
                    $totalhomefeedskey = 'discover/homefeed/getList/'.$auth_id.':'.$auth_id.'_totallist';

                    $auth_interest = $user->interest ? $user->interest : 'Both';
                    $radius = $user->discover_distance;
                    $latitude = $user->current_latitude?$user->current_latitude:$user->latitude;
                    $longitude = $user->current_longitude?$user->current_longitude:$user->longitude;
                    $last7thDate = (new Carbon)->subDays(7)->startOfDay();
                    $last30thDate = (new Carbon)->subDays(30)->startOfDay();
                    $currentDate = (new Carbon)->now()->endOfDay();
                    $profile_ranking = $user->discover_profile_ranking;
                    $hasPhoto     =     $user->discover_has_photo;
                    $searchNearMe = $user->discover_search_near_me;
                    $searchByState = $user->discover_by_state;
                    $state = $user->discover_state;
                    $onlineStatus = $user->discover_online_status;
                    $relationStatus = $user->discover_relationship_status;
                    $education = $user->discover_education;
                    $getListData = $this->redis->get($homefeedskey);
                    $data=[];
                    if($getListData){
                        $data['users'] = json_decode($getListData);
                        $data['count'] = $this->redis->get($totalhomefeedskey);
                    }else{

                        $languages  = UserSetting::whereUserId($auth_id)->whereNotNull('language_id')->distinct()->pluck('language_id')->toArray();
                        if(empty($languages)){
                            $languages[]  = $user->language_id;
                        }

                       $disLikes   =   DisLike::whereDisLikerId($auth_id)->whereBetween('updated_at', [$last7thDate, $currentDate])
                        ->whereNotNull('user_id')->distinct()->pluck('user_id')->toArray();
                       $likes   =   Like::select('user_id')->whereLikerId($auth_id)
                        ->where('is_superlike','n')->whereBetween('updated_at', [$last7thDate, $currentDate])
                        ->whereNotNull('user_id')->distinct()->pluck('user_id')->toArray();
                        
                       $superlikes   =   Like::select(DB::raw('(CASE WHEN `liker_id` = '.$auth_id.' THEN `user_id` ELSE `liker_id` END) AS user_id'))->where(function($query)use($auth_id){
                            $query->where('liker_id',$auth_id);
                            $query->orWhere('user_id',$auth_id);
                        })
                        ->where('is_superlike','y')->distinct()->pluck('user_id')->toArray();

                       $reported = ProfileReport::select('reported_user_id')->where('user_id', $auth_id)
                        ->whereBetween('updated_at', [$last30thDate, $currentDate])
                        ->whereNotNull('user_id')->distinct()->pluck('reported_user_id')->toArray();

                        $exclusiveData['latitude'] = $latitude;
                        $exclusiveData['longitude'] = $longitude;
                        $exclusiveData['radius'] = $radius;
                        $exclusiveData['languages'] = $languages;
                        $exclusiveData['disLikes'] = $disLikes;
                        $exclusiveData['likes'] = $likes;
                        $exclusiveData['superlikes'] = $superlikes;
                        $exclusiveData['reported'] = $reported;
                        $exclusiveData['auth_id'] = $auth_id;
                        $exclusiveData['auth_interest'] = $auth_interest;
                        $exclusiveData['profile_ranking'] = $profile_ranking;
                        $exclusiveData['hasPhoto'] =$hasPhoto;
                        $exclusiveData['searchNearMe'] = $searchNearMe;
                        $exclusiveData['searchByState'] = $searchByState;
                        $exclusiveData['state'] = $state;
                        $exclusiveData['onlineStatus'] = $onlineStatus;
                        $exclusiveData['relationStatus'] = $relationStatus;
                        $exclusiveData['education'] = $education;
                        $authenticateNewUser = $user->areAllColumnsNull();
                        // dd($authenticateNewUser);
                        if($authenticateNewUser){
                            $data = $this->getNewlyUserHomeFeedCardData($user, $exclusiveData,$request->limit,$request->offset);
                        }else{
                            $data = $this->getHomeFeedCardData($user, $exclusiveData,$request->limit,$request->offset);
                        }
                        // dd($data);
                        if($data['users']->isEmpty()){
                            $data = $this->callUsersOfCountry($user, $exclusiveData,$request->limit,$request->offset); 
                        }
                        // dd(DB::getQueryLog());
                        if($data['users']->isNotEmpty()){
                            $jsonData = json_encode($data['users']->toArray());
                            // $data['users'] = json_decode($jsonData);
                            $this->redis->set($totalhomefeedskey, $data['count']); 
                            $this->redis->set($homefeedskey, $jsonData);
                            $data['users'] = json_decode($jsonData);
                            $data['count'] = $this->redis->get($totalhomefeedskey);
                        } 
                    }

                    $is_profile_photo = false;
                    if ($user->profile_photo != '') {
                        $is_profile_photo = true;
                    }
                    // dd($data['users']);
                    if (!empty($data['users'])) {
                        return (HomeResource::collection($data['users']))->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $data['count'],
                                'is_swipe_allow' =>  $is_swipe_allow,
                                'is_profile_photo' =>  $is_profile_photo,
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'is_ban'    =>  false,
                                'dummy_status'=> true,
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.list', ['entity' => __('Users')]),
                            ]
                        ]);
                    } else {
                        $this->response['meta']['is_swipe_allow'] = $is_swipe_allow;
                        $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Users')]);
                        $this->response['meta']['is_ban']  = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                    }
                } else {
                    $this->response['meta']['is_swipe_allow'] = $is_swipe_allow;
                    $this->response['meta']['message']  =   trans('api.swipe_over');
                    $this->response['meta']['is_ban']  = false;
                    $this->status = Response::HTTP_FORBIDDEN;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                        $this->response['meta']['is_ban']  = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban']  = false;
                        break;
                };
            } catch (\Exception $e) {
                throw $e;
                $this->storeErrorLog($e, 'get_home_feed');
            }
        }
        return $this->returnResponse();
    } 

    public function getNewlyUserHomeFeedCardData($user, $exclusiveData, $limit, $offset){
        if($user->discover_location_id == NULL && $user->location_id != NULL){
            $user->discover_location_id = $user->location_id;
        }
        $getUserLocation = $this->getUserState($user->discover_location_id);
        $city = $getUserLocation->name;
        $state = $getUserLocation->state;
        $country = $getUserLocation->country;
        $latitude = $exclusiveData['latitude'];
        $longitude = $exclusiveData['longitude'];
        $radius = $exclusiveData['radius'];
        $languages = $exclusiveData['languages'];
        $disLikes = $exclusiveData['disLikes'];
        $likes    = $exclusiveData['likes'];
        $reported = $exclusiveData['reported'];
        $auth_id  = $exclusiveData['auth_id'];
        $auth_interest = $exclusiveData['auth_interest'];
        $location_id = $user->location_id;
        $discover_location_id = $user->discover_location_id;


        // First, try to find nearby users
        $users = User::select('users.id',
            'users.custom_id',
            'birth_date',
            'profile_photo',
            'gender',
            'interest',
            'users.location_id',
            'language_id',
            'verify_status',
            'verify_photo_status',
            'verify_email_send',
            'last_online',
            'users.created_at',
            'users.voice',

            'trusted_score',
            'email_verified_at',

            'contact_verified_at',

            'profile_percentage',

            'users.is_active',)
            ->with(['userDetails:custom_id,user_id,image,video,sequence,is_verified','interests.interest.interestTranslation:title', 'userTranslation','location.locationTranslation:id,location_id,state,name,country'])
            ->selectRaw('( 6371 * acos( cos( radians(?) ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( users.latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            ->where(function ($query) use ($latitude, $longitude, $radius, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                 $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);

            })->orWhere(function ($query) use ($city, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                $query->whereHas('location.locationTranslation',function ($q) use ($city){
                    $q->where('name', $city);
                });
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);
            })->orWhere(function ($query) use ($state, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                $query->whereHas('location.locationTranslation',function ($q) use ($state){
                    $q->where('state', $state);
                });
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);
            })->orWhere(function ($query) use ($country, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                $query->whereHas('location.locationTranslation',function ($q) use ($country){
                    $q->where('country', $country);
                });
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);
            });

            if ($user->location_id == $user->discover_location_id
            || $user->discover_location_id == null) {

                if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
                    $users->having("distance", "<=", $radius);
                    $users = $users->orderBy('distance');
                    
                }
            }

            $data['count'] = $users->count();
            $data['users'] = $users->limit($limit ?? config('utility.pagination.limit'))
            ->offset($offset ?? config('utility.pagination.offset'))
            ->get();  
            // dd(DB::getQueryLog());
        return  $data;
    }

    public function getHomeFeedCardData($user, $exclusiveData, $limit, $offset){
        if($user->discover_location_id == NULL && $user->location_id != NULL){
            $user->discover_location_id = $user->location_id;
        }
        $latitude = $exclusiveData['latitude'];
        $longitude = $exclusiveData['longitude'];
        $radius = $exclusiveData['radius'];
        $languages = $exclusiveData['languages'];
        $disLikes = $exclusiveData['disLikes'];
        $likes    = $exclusiveData['likes'];
        $reported = $exclusiveData['reported'];
        $auth_id  = $exclusiveData['auth_id'];
        $superlikes = $exclusiveData['superlikes'];
        $auth_interest = $exclusiveData['auth_interest'];

        $getUserLocation = $this->getUserState($user->discover_location_id);
        $city = $getUserLocation->name;
        $state = $getUserLocation->state;
        $country = $getUserLocation->country;

        $discover_start_age = $user->discover_start_age;
        $discover_end_age = $user->discover_end_age;

        $profile_ranking = $exclusiveData['profile_ranking'];
        $hasPhoto = $exclusiveData['hasPhoto'];
        $searchNearMe = $exclusiveData['searchNearMe'];
        $searchByState = $exclusiveData['searchByState'];
        $discover_state = $exclusiveData['state'];
        $onlineStatus = $exclusiveData['onlineStatus'];
        $endDate = Carbon::now()->toDateString();
        $startDate = Carbon::now()->subDays(7)->toDateString();
        $online_time_limit = config('utility.profile.durations.online_time');
        $recent_time_limit = config('utility.profile.durations.recent_online_time');
        $location_id = $user->location_id;
        $discover_location_id = $user->discover_location_id;

        if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
            if((int)$searchNearMe == 1){
                $radius = 10;
            }
        }

        
        // First, try to find nearby users
        $users = User::select('users.id',
            'users.custom_id',
            'birth_date',
            'profile_photo',
            'gender',
            'interest',
            'users.location_id',
            'language_id',
            'verify_status',
            'verify_photo_status',
            'verify_email_send',
            'last_online',
            'users.created_at',
            'users.voice',
            'trusted_score',
            'email_verified_at',

            'contact_verified_at',

            'profile_percentage',

            'users.is_active')
            ->with(['userDetails:custom_id,user_id,image,video,sequence,is_verified','interests.interest.interestTranslation:title', 'userTranslation','location.locationTranslation:id,location_id,state,name,country'])
            ->withCount('likes')
            ->withCount(['interests' => function($q) use($auth_id) {
                $q->whereIn('interest_id', function($query1) use ($auth_id){
                    $query1->select('interest_id')
                          ->from('user_interests')
                          ->where('user_id','=',$auth_id);
                });
            }])
            ->selectRaw('( 6371 * acos( cos( radians(?) ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( users.latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            ->where(function ($query) use ($discover_start_age, $discover_end_age, $onlineStatus, $startDate, $endDate, $discover_state, $latitude, $longitude, $radius, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id, $searchByState, $profile_ranking) {
                
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);

                if((int)$searchByState == 1){
                    $query->whereHas('location.locationTranslation',function ($q) use ($discover_state){
                        $q->where('state', $discover_state);
                    });
                }
                // $query->whereNotNull('users.latitude')
                //       ->whereNotNull('users.longitude');
                if (!empty($discover_start_age) && !empty($discover_end_age)) {

                    $query->whereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'), array($discover_start_age, $discover_end_age));
                }
                if((int)$profile_ranking == 1){
                    $query->orWhereBetween('users.created_at',[$startDate, $endDate]);
                }

                if((int)$onlineStatus == 1){
                    $query->orWhereBetween('users.last_online',[Carbon::now()->subMinutes(1), Carbon::now()])->where('users.id','!=',$auth_id);
                  
                  //online today
                  }else if((int)$onlineStatus == 2){
                      $query->orWhereBetween('users.last_online',[Carbon::now()->subHours(24), Carbon::now()])->where('users.id','!=',$auth_id);
          
                  //online this week    
                  }else if((int)$onlineStatus == 3){
                      $query->orWhereBetween('users.last_online',[Carbon::now()->subDays(7), Carbon::now()])->where('users.id','!=',$auth_id);
                      
                  }
                
            })->orWhere(function ($query) use ($city, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);
                $query->whereHas('location.locationTranslation',function ($q) use ($city){
                    $q->where('name', $city);
                });
                
                
            })->orWhere(function ($query) use ($state, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);
                $query->whereHas('location.locationTranslation',function ($q) use ($state){
                    $q->where('state', $state);
                });
                
                
            })->orWhere(function ($query) use ($country, $auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id) {
                
                $query->commonGrouping($auth_interest, $disLikes, $likes, $reported, $auth_id, $languages, $location_id, $discover_location_id);
                $query->whereHas('location.locationTranslation',function ($q) use ($country){
                    $q->where('country', $country);
                });
                
               
            });
           
            $users->withTrashed(); 
            if ($user->location_id == $user->discover_location_id
            || $user->discover_location_id == null) {

                if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
                    $users->having("distance", "<=", $radius);
                    $users = $users->orderBy('distance');
                    
                }
            }               
            $users = $users->having('interests_count','>',1)
                       ->orHaving('interests_count','>',1)
                       ->orHaving('interests_count','=',0);
            if((int)$searchByState == false){
                if (!empty($user->discover_location_id)) {
                    $users = $users->orderByRaw('location_id = '.$user->discover_location_id.' DESC');
                } 
           } 
           
           if((int)$profile_ranking == 2){
            $users->orderBy('likes_count','DESC');
           }
            
           $users = $users->orderBy('last_online','DESC')
                          ->orderBy('email_verified_at', "DESC")
                          ->orderBy('contact_verified_at', "DESC")
                          ->orderBy('photo_verified_at', "DESC")
                          ->orderBy('profile_percentage', "DESC");
            
            $data['count'] = $users->count();
            $data['users'] = $users->limit($limit ?? config('utility.pagination.limit'))
            ->offset($offset ?? config('utility.pagination.offset'))
            ->get(); 
            return $data; 
       
    }

    public function fetchHomeCardData($user, $exclusiveData, $limit, $offset){
        $latitude = $exclusiveData['latitude'];
        $longitude = $exclusiveData['longitude'];
        $radius = $exclusiveData['radius'];
        $languages = $exclusiveData['languages'];
        $disLikes = $exclusiveData['disLikes'];
        $likes    = $exclusiveData['likes'];
        $reported = $exclusiveData['reported'];
        $auth_id  = $exclusiveData['auth_id'];
        $superlikes = $exclusiveData['superlikes'];
        $auth_interest = $exclusiveData['auth_interest'];

        $profile_ranking = $exclusiveData['profile_ranking'];
        $hasPhoto = $exclusiveData['hasPhoto'];
        $searchNearMe = $exclusiveData['searchNearMe'];
        $searchByState = $exclusiveData['searchByState'];
        $state = $exclusiveData['state'];
        $onlineStatus = $exclusiveData['onlineStatus'];
        $relationStatus = $exclusiveData['relationStatus'];
        $education = $exclusiveData['education'];
        $endDate = Carbon::now()->toDateString();
        $startDate = Carbon::now()->subDays(7)->toDateString();
        $online_time_limit = config('utility.profile.durations.online_time');
        $recent_time_limit = config('utility.profile.durations.recent_online_time');
        // dd(Carbon::now()->subDays(7));
        // dd(Carbon::now());
        // dd(time(), $online_time_limit, $recent_time_limit);



        if (!empty($radius) && !empty($latitude) && !empty($longitude)) {

            if((int)$searchNearMe == 1){
                $radius = 10;
            }

            $users = $this->withRadius($latitude, $longitude, $radius, $user,$state);
            
        } else {
            
            $users = $this->withoutRadius();
        }
        
        $users = $users->with(['userDetails:custom_id,user_id,image,video,sequence,is_verified','interests.interest.interestTranslation:title', 'user.userTranslation','location.locationTranslation:id,location_id,state,name']);
        $users = $users->withCount('likes');

        $users = $users->withCount(['interests' => function($q) use($auth_id) {
            $q->whereIn('interest_id', function($query1) use ($auth_id){
                $query1->select('interest_id')
                      ->from('user_interests')
                      ->where('user_id','=',$auth_id);
            });
        }]);

            // if (!empty($user->discover_location_id)) {
            //     if ($user->location_id != $user->discover_location_id) {
            //         $users->where('location_id', $user->discover_location_id);  // Location
            //     }
            // }
            // dd((int)$profile_ranking);
            

            $users->where('users.id', '!=', $auth_id)
                            ->whereNotNull('profile_photo')
                            ->whereNotNull('location_id')
                            ->whereIsActive('y')
                            ->where('id','!=',config('utility.system.system_user_id'))
                            ->whereUserStatus('active');
            $users->whereNull('users.deleted_at');
            
            if ($auth_interest != 'Both') {
                $users->where('gender', $auth_interest);
            }     // Interested in Gender
            
        
            if (count($disLikes) > 0) {
                $users->whereNotIn('users.id', $disLikes);    // Restrict DisLiked Profile
            }
    
            if (count($likes) > 0) {
                $users->whereNotIn('users.id', $likes);   // Restrict Liked Profile
            }
    
            if (count($reported) > 0) {
                $users->whereNotIn('users.id', $reported);    // Restrict Reported Profile
            }
    
            if (count($superlikes) > 0) {
                $users->whereNotIn('users.id', $superlikes);   // Restrict Super Liked Profiles - Both Ways
            }
    
            $users->whereDoesntHave('blockedTos',function($query1)use($auth_id){
                $query1->where('block_by',$auth_id);
            });
    
            $users->whereDoesntHave('hiddenTos',function($query2)use($auth_id){
                $query2->where('block_by',$auth_id);
            });                                          
    
        // Discovery
            if (!empty($user->discover_start_age) && !empty($user->discover_end_age)) {

                $users->whereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'), array($user->discover_start_age, $user->discover_end_age));
            }
    
            $users->where(function ($query3)  use ($languages) {
                if (count($languages) > 0) {
                    $query3->orWhereIn('language_id', $languages);   // Languages
                }
            });
    
            if ($user->location_id == $user->discover_location_id|| $user->discover_location_id == null) {
                $users->whereNotNull('users.latitude');
                $users->whereNotNull('users.longitude');
            }


        ///City Condition
        $users->where(function($query) use ($user,$auth_id,$languages,$superlikes,$auth_interest,$disLikes,$likes,$reported,$searchByState,$state){
            
            if((int)$searchByState == 1){
                $query->whereIn('location_id',function ($queryD) use ($user,$state) {
                    $queryD->select(['lt.location_id'])
                        ->from('locations as loc')
                        ->join('location_translations as lt','loc.id','=','lt.location_id')
                        ->where('lt.state','=',$state)
                        ->where('loc.is_active','=','y')
                        ->where('lt.locale','=','en');
                });

            }else{
                if($user->discover_location_id == NULL && $user->location_id != NULL){
                    $user->discover_location_id = $user->location_id;
                }

                if($user->discover_location_id != NULL){

                    $query->whereIn('location_id',function ($query1) use ($user) {
                        $query1->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('lt.name', function($query2) use ($user){
                                $query2->select('name')
                                        ->from('location_translations')
                                        ->join('locations','location_translations.location_id', '=' ,'locations.id')
                                        ->where('location_id','=',$user->discover_location_id)
                                        ->where('locations.is_active','=','y')
                                        ->where('location_translations.locale','=','en');
                            });
                    });
                    $query->orWhereIn('location_id',function ($queryD) use ($user) {
                        $queryD->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('lt.state', function($queryE) use ($user){
                                $queryE->select('state')
                                        ->from('location_translations')
                                        ->join('locations','location_translations.location_id', '=' ,'locations.id')
                                        ->where('location_id','=',$user->discover_location_id)
                                        ->where('locations.is_active','=','y')
                                        ->where('location_translations.locale','=','en');
                            });
                    });

                }else{

                    $query->whereIn('location_id',function ($queryF) use ($user) {
                        $queryF->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('loc.is_active','=','y')
                            ->where('lt.locale','=','en');
                    });
                    
                }
            }

            
        }); 
        if((int)$profile_ranking == 1){
            $users->orWhereBetween('users.created_at',[$startDate, $endDate]);
            $users->where(function($query) use ($user,$auth_id,$languages,$superlikes,$auth_interest,$disLikes,$likes,$reported,$searchByState,$state){
                if((int)$searchByState == 1){
                    $query->whereIn('location_id',function ($queryH) use ($user,$state) {
                        $queryH->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('lt.state','=',$state)
                            ->where('loc.is_active','=','y')
                            ->where('lt.locale','=','en');
                    });
                }else{
                    if($user->discover_location_id == NULL && $user->location_id != NULL){
                        $user->discover_location_id = $user->location_id;
                    }
                    
                    if($user->discover_location_id != NULL){
    
                        $query->whereIn('location_id',function ($query1) use ($user) {
                            $query1->select(['lt.location_id'])
                                ->from('locations as loc')
                                ->join('location_translations as lt','loc.id','=','lt.location_id')
                                ->where('lt.name', function($query2) use ($user){
                                    $query2->select('name')
                                            ->from('location_translations')
                                            ->join('locations','location_translations.location_id', '=' ,'locations.id')
                                            ->where('location_id','=',$user->discover_location_id)
                                            ->where('locations.is_active','=','y')
                                            ->where('location_translations.locale','=','en');
                                });
                        });
                        $query->orWhereIn('location_id',function ($queryD) use ($user) {
                            $queryD->select(['lt.location_id'])
                                ->from('locations as loc')
                                ->join('location_translations as lt','loc.id','=','lt.location_id')
                                ->where('lt.state', function($queryE) use ($user){
                                    $queryE->select('state')
                                            ->from('location_translations')
                                            ->join('locations','location_translations.location_id', '=' ,'locations.id')
                                            ->where('location_id','=',$user->discover_location_id)
                                            ->where('locations.is_active','=','y')
                                            ->where('location_translations.locale','=','en');
                                });
                        });
    
                    }else{
    
                        $query->whereIn('location_id',function ($queryF) use ($user) {
                            $queryF->select(['lt.location_id'])
                                ->from('locations as loc')
                                ->join('location_translations as lt','loc.id','=','lt.location_id')
                                ->where('loc.is_active','=','y')
                                ->where('lt.locale','=','en');
                        });
                        
                    }
                }
              });
        }
        
        // dd($onlineStatus);
        //online now
        if((int)$onlineStatus == 1){
          $users->orWhereBetween('users.last_online',[Carbon::now()->subMinutes(1), Carbon::now()])->where('users.id','!=',$auth_id);
        
        //online today
        }else if((int)$onlineStatus == 2){
            $users->orWhereBetween('users.last_online',[Carbon::now()->subHours(24), Carbon::now()])->where('users.id','!=',$auth_id);

        //online this week    
        }else if((int)$onlineStatus == 3){
            $users->orWhereBetween('users.last_online',[Carbon::now()->subDays(7), Carbon::now()])->where('users.id','!=',$auth_id);
            
        }

        if((int)$onlineStatus == 1||(int)$onlineStatus == 2||(int)$onlineStatus == 3 || (int)$profile_ranking == 1){
            $users->where(function($query) use ($user,$auth_id,$languages,$superlikes,$auth_interest,$disLikes,$likes,$reported,$searchByState,$state){
            if((int)$searchByState == 1){
                $query->whereIn('location_id',function ($queryH) use ($user,$state) {
                    $queryH->select(['lt.location_id'])
                        ->from('locations as loc')
                        ->join('location_translations as lt','loc.id','=','lt.location_id')
                        ->where('lt.state','=',$state)
                        ->where('loc.is_active','=','y')
                        ->where('lt.locale','=','en');
                });
            }else{
                if($user->discover_location_id == NULL && $user->location_id != NULL){
                    $user->discover_location_id = $user->location_id;
                }
                // dd($auth_id,$user->discover_location_id);
                if($user->discover_location_id != NULL){

                    $query->whereIn('location_id',function ($query1) use ($user) {
                        $query1->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('lt.name', function($query2) use ($user){
                                $query2->select('name')
                                        ->from('location_translations')
                                        ->join('locations','location_translations.location_id', '=' ,'locations.id')
                                        ->where('location_id','=',$user->discover_location_id)
                                        ->where('locations.is_active','=','y')
                                        ->where('location_translations.locale','=','en');
                            });
                    });
                    $query->orWhereIn('location_id',function ($queryD) use ($user) {
                        $queryD->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('lt.state', function($queryE) use ($user){
                                $queryE->select('state')
                                        ->from('location_translations')
                                        ->join('locations','location_translations.location_id', '=' ,'locations.id')
                                        ->where('location_id','=',$user->discover_location_id)
                                        ->where('locations.is_active','=','y')
                                        ->where('location_translations.locale','=','en');
                            });
                    });

                }else{

                    $query->whereIn('location_id',function ($queryF) use ($user) {
                        $queryF->select(['lt.location_id'])
                            ->from('locations as loc')
                            ->join('location_translations as lt','loc.id','=','lt.location_id')
                            ->where('loc.is_active','=','y')
                            ->where('lt.locale','=','en');
                    });
                    
                }
            }
          });
        }
       

        // if(!empty($relationStatus)){

        //     $users->orWhere('relationship_status_id',$relationStatus);
        // }

        // if(!empty($education)){

        //     $users->orWhere('education_id',$education);
        // }   
        
        $users->withTrashed();                
        $users = $users->having('interests_count','>',1)
                       ->orHaving('interests_count','>',1)
                       ->orHaving('interests_count','=',0);
    if((int)$searchByState == false){
        if (!empty($user->discover_location_id)) {
            if ($user->location_id != $user->discover_location_id) {
                $users = $users->orderByRaw('location_id = '.$user->discover_location_id.' DESC');
            }else{
                $users = $users->orderByRaw('location_id = '.$user->discover_location_id.' DESC');
            }
        } 
    }

        
    $getUserState = $this->getUserState($user->location_id);
    if($state != null){
        if($state === $getUserState->state){

            if (
                $user->location_id == $user->discover_location_id|| 
                $user->discover_location_id == null
            ) {
                if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
                        
                    $users = $users->orderBy('distance');
                    
                }
            }
        }
    }else{
        if (
            $user->location_id == $user->discover_location_id|| 
            $user->discover_location_id == null
        ) {
            if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
                        
                $users = $users->orderBy('distance');
                
            }
        }
    }
        
       // $users = $users->orderBy('interests_count', "DESC");
       if((int)$profile_ranking == 2){
        $users->orderBy('likes_count','DESC');
       }
        
        $users = $users->orderBy('last_online','DESC')
                       ->orderBy('email_verified_at', "DESC")
                       ->orderBy('contact_verified_at', "DESC")
                       ->orderBy('photo_verified_at', "DESC")
                       ->orderBy('profile_percentage', "DESC");
           
               
        
        
        $data['count'] = $users->count();
        $data['users'] = $users->limit($limit ?? config('utility.pagination.limit'))
        ->offset($offset ?? config('utility.pagination.offset'))
        ->get();
        
        // dd($auth_id,DB::getQueryLog(),Carbon::now()->subMinutes(1), Carbon::now());
        return  $data;   
    }

    public function callUsersOfCountry($user, $exclusiveData, $limit, $offset){

        $latitude = $exclusiveData['latitude'];
        $longitude = $exclusiveData['longitude'];
        $radius = $exclusiveData['radius'];
        $languages = $exclusiveData['languages'];
        $disLikes = $exclusiveData['disLikes'];
        $likes    = $exclusiveData['likes'];
        $reported = $exclusiveData['reported'];
        $auth_id  = $exclusiveData['auth_id'];
        $superlikes = $exclusiveData['superlikes'];
        $auth_interest = $exclusiveData['auth_interest'];
        if (!empty($radius) && !empty($latitude) && !empty($longitude)) {               
            $users =  User::select(
                'users.id',
                'users.custom_id',
                'birth_date',
                'profile_photo',
                'gender',
                'interest',
                'location_id',
                'language_id',
                'verify_status',
                'verify_photo_status',
                'verify_email_send',
                'last_online',
                'created_at',
    
                'trusted_score',
                'email_verified_at',
    
                'contact_verified_at',
    
                'profile_percentage',
    
                'is_active',
                // DB::raw('GROUP_CONCAT(user_interests.interest_id) AS groupC'),
    
                DB::raw("3959 * 1.609344 * acos(cos(radians(" . $latitude . ")) 
                * cos(radians(users.latitude)) 
                * cos(radians(users.longitude) - radians(" . $longitude . ")) 
                + sin(radians(" . $latitude . ")) 
                * sin(radians(users.latitude))) AS distance")
            );

        } else {   
            $users = $this->withoutRadius();
        }
        $users = $users->with(['userDetails', 'interests.interest.interestTranslation', 'userTranslation', 'location.locationTranslation']);

        $users = $users->withCount(['interests' => function($q) use($auth_id) {
            $q->whereIn('interest_id', function($query1) use ($auth_id){
                $query1->select('interest_id')
                        ->from('user_interests')
                        ->where('user_id','=',$auth_id);
            });
        }]);

        $users->where('users.id', '!=', $auth_id)
                ->whereNotNull('profile_photo')
                ->whereNotNull('location_id')
                ->whereIsActive('y')
                ->where('id','!=',config('utility.system.system_user_id'))
                ->whereUserStatus('active');
        
        if ($auth_interest != 'Both') {
            $users->where('gender', $auth_interest);
        }     // Interested in Gender


        if (count($disLikes) > 0) {
            $users->whereNotIn('users.id', $disLikes);    // Restrict DisLiked Profile
        }

        if (count($likes) > 0) {
            $users->whereNotIn('users.id', $likes);   // Restrict Liked Profile
        }

        if (count($superlikes) > 0) {
            $users->whereNotIn('users.id', $superlikes);   // Restrict Super Liked Profiles - Both Ways
        }

        if (count($reported) > 0) {
            $users->whereNotIn('users.id', $reported);    // Restrict Reported Profile
        }

        $users->whereDoesntHave('blockedTos',function($query)use($auth_id){
            $query->where('block_by',$auth_id);
        });

        $users->whereDoesntHave('hiddenTos',function($query)use($auth_id){
            $query->where('block_by',$auth_id);
        });

        $users->whereIn('location_id',function ($query) use ($user) {
            $query->select(['lt.location_id'])
                ->from('locations as loc')
                ->join('location_translations as lt','loc.id','=','lt.location_id')
                ->where('loc.is_active','=','y')
                ->where('lt.locale','=','en');
        });
                                                
        
        // Discovery
        if (!empty($user->discover_start_age) && !empty($user->discover_end_age)) {
            
            $users->whereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'), array($user->discover_start_age, $user->discover_end_age));
        }


        $users->where(function ($query)  use ($languages) {
            if (count($languages) > 0) {
                $query->orWhereIn('language_id', $languages);   // Languages
            }
        });

        $users = $users->orHaving('interests_count','>',1)
                       ->orHaving('interests_count','=',0);

        $users = $users->orderBy('interests_count', "DESC")
                    ->orderBy('last_online','DESC')
                    ->orderBy('email_verified_at', "DESC")
                    ->orderBy('contact_verified_at', "DESC")
                    ->orderBy('photo_verified_at', "DESC")
                    ->orderBy('profile_percentage', "DESC");

        $data['count'] = $users->count();
        $data['users'] = $users->limit($limit ?? config('utility.pagination.limit'))
            ->offset($offset ?? config('utility.pagination.offset'))
            ->get();
        return  $data; 
        
    }

    public function withRadius($latitude, $longitude, $radius, $user, $state){

        $users =  User::select(
            'users.id',
            'users.custom_id',
            'birth_date',
            'profile_photo',
            'gender',
            'interest',
            'location_id',
            'language_id',
            'verify_status',
            'verify_photo_status',
            'verify_email_send',
            'last_online',
            'created_at',

            'trusted_score',
            'email_verified_at',

            'contact_verified_at',

            'profile_percentage',

            'is_active',
            // DB::raw('GROUP_CONCAT(user_interests.interest_id) AS groupC'),

            DB::raw("3959 * 1.609344 * acos(cos(radians(" . $latitude . ")) 
            * cos(radians(users.latitude)) 
            * cos(radians(users.longitude) - radians(" . $longitude . ")) 
            + sin(radians(" . $latitude . ")) 
            * sin(radians(users.latitude))) AS distance")
        );
        $getUserState = $this->getUserState($user->location_id);
        // dd($getUserState->state,$state);
        if($state != null){
            if($state === $getUserState->state){

                if (
                    $user->location_id == $user->discover_location_id|| 
                    $user->discover_location_id == null
                ) {
                        $users->having("distance", "<=", $radius);
                }
            }
        }else{
            if (
                $user->location_id == $user->discover_location_id|| 
                $user->discover_location_id == null
            ) {
                    $users->having("distance", "<=", $radius);
            }
        }
        

        

        return $users;
    }

    public function getUserState($location_id)
    {
       $location =  LocationTranslation::select('state','name','country')->where(['location_id'=>$location_id,'locale'=>'en'])->first();
       
       return $location;
    }

    public function withoutRadius(){

        return User::select(
            'users.id',
            'users.custom_id',
            'birth_date',
            'profile_photo',
            'gender',
            'verify_photo_status',
            'verify_email_send',
            'email_verified_at',
            'contact_verified_at',
            'trusted_score',
            'interest',
            'location_id',
            'language_id',
            'verify_status',
            'is_active',
            'profile_percentage'
        );

        if (!empty($user->discover_location_id)) {
            if ($user->location_id != $user->discover_location_id) {
                $users->where('location_id', $user->discover_location_id);  // Location
            }
        }
    }
}
