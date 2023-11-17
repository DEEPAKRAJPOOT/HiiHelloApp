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
use App\Models\{User, BlockUser, UserSetting, DisLike};
use Illuminate\Database\Eloquent\{ModelNotFoundException};

class HomeController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
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
                    $auth_interest = $user->interest ? $user->interest : 'Both';
                    $radius = $user->discover_distance;
                    $latitude = $user->current_latitude;
                    $longitude = $user->current_longitude;
                    
                    $last7thDate = (new Carbon)->subDays(7)->startOfDay();
                    $last30thDate = (new Carbon)->subDays(30)->startOfDay();
                    $currentDate = (new Carbon)->now()->endOfDay();

                    // $blocked    =   BlockUser::whereBlockBy($auth_id)->whereNotNull('blocked_to')->distinct()->pluck('blocked_to')->toArray();
                    $languages  =   UserSetting::whereUserId($auth_id)->whereNotNull('language_id')->distinct()->pluck('language_id')->toArray();
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
                        // dd($radius,$latitude,$longitude);
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
                    $data = $this->fetchHomeCardData($user, $exclusiveData,$request->limit,$request->offset);
                       
                    if($data['users']->isEmpty()){
                        $data = $this->callUsersOfCountry($user, $exclusiveData,$request->limit,$request->offset); 
                    }

                    $is_profile_photo = false;
                    if ($user->profile_photo != '') {
                        $is_profile_photo = true;
                    }

                    if ($data['users']->isNotEmpty()) { //return $users;
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
        if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
                        
            $users = $this->withRadius($latitude, $longitude, $radius, $user);
            
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

        $users = $users->where('users.id', '!=', $auth_id)
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

        if (count($reported) > 0) {
            $users->whereNotIn('users.id', $reported);    // Restrict Reported Profile
        }

        if (count($superlikes) > 0) {
            $users->whereNotIn('users.id', $superlikes);   // Restrict Super Liked Profiles - Both Ways
        }

        $users->whereDoesntHave('blockedTos',function($query)use($auth_id){
            $query->where('block_by',$auth_id);
        });

        $users->whereDoesntHave('hiddenTos',function($query)use($auth_id){
            $query->where('block_by',$auth_id);
        });

    // if (count($blocked) > 0) {
    //     $users->whereNotIn('id', $blocked);     // Restrict Blocked Profile
    // }                                           

    // Discovery
        if (!empty($user->discover_start_age) && !empty($user->discover_end_age)) {
            // $users->whereBetween('birth_date', array($user->discover_start_age, $user->discover_end_age)); // Age
            $users->whereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'), array($user->discover_start_age, $user->discover_end_age));
        }

        $users->where(function ($query)  use ($languages) {
            if (count($languages) > 0) {
                $query->orWhereIn('language_id', $languages);   // Languages
            }
        });
///City Condition
        $users->orWhereIn('location_id',function ($query) use ($user) {
            $query->select(['lt.location_id'])
                ->from('locations as loc')
                ->join('location_translations as lt','loc.id','=','lt.location_id')
                ->where('lt.name', function($query1) use ($user){
                    $query1->select('name')
                            ->from('location_translations')
                            ->join('locations','location_translations.location_id', '=' ,'locations.id')
                            ->where('location_id','=',$user->discover_location_id)
                            ->where('locations.is_active','=','y')
                            ->where('location_translations.locale','=','en');
                });
        });
///State Condition
        $users->orWhereIn('location_id',function ($query) use ($user) {
            $query->select(['lt.location_id'])
                ->from('locations as loc')
                ->join('location_translations as lt','loc.id','=','lt.location_id')
                ->where('lt.state', function($query1) use ($user){
                    $query1->select('state')
                            ->from('location_translations')
                            ->join('locations','location_translations.location_id', '=' ,'locations.id')
                            ->where('location_id','=',$user->discover_location_id)
                            ->where('locations.is_active','=','y')
                            ->where('location_translations.locale','=','en');
                });
        });

        

        $users = $users->having('interests_count','>',1)
                       ->orHaving('interests_count','>',1)
                       ->orHaving('interests_count','=',0);

        if (!empty($user->discover_location_id)) {
            if ($user->location_id != $user->discover_location_id) {
                $users = $users->orderByRaw('location_id = '.$user->discover_location_id.' DESC');
            }
        }    
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
        $with_interest = $exclusiveData['with_interest'];
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
        if($with_interest){

            $users = $users->withCount(['interests' => function($q) use($auth_id) {
                $q->whereIn('interest_id', function($query1) use ($auth_id){
                    $query1->select('interest_id')
                          ->from('user_interests')
                          ->where('user_id','=',$auth_id);
                });
            }]);
        }

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

    public function withRadius($latitude, $longitude, $radius, $user){

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
        
            if ($user->location_id == $user->discover_location_id) {
                    $users->whereNotNull('users.latitude');
                    $users->whereNotNull('users.longitude');
                    $users->having("distance", "<=", $radius);
                    $users = $users->orderBy('distance');
            }

        if (!empty($user->discover_location_id)) {
            if ($user->location_id != $user->discover_location_id) {
                $users->where('location_id', $user->discover_location_id);  // Location
            }
        }

        return $users;
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
