<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Resources\v1\{HomeResource};
use Illuminate\Support\Facades\{DB};
use App\Http\Requests\Api\General\{PaginationRequest};
use App\Models\{User, BlockUser, UserSetting, DisLike};

class HomeController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get All Users List
    public function getHomeFeeds(Request $request)
    {
        $paginationRequest = new PaginationRequest();
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
            try {
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;
                $is_swipe_allow = $user->isSwipeAllow();

                if ($is_swipe_allow) {
                    $auth_interest = $user->interest ? $user->interest : 'Both';
                    $radius = $user->discover_distance;
                    
                    $latitude = $user->current_latitude;
                    $longitude = $user->current_longitude;

                    // $blocked    =   BlockUser::whereBlockBy($auth_id)->whereNotNull('blocked_to')->distinct()->pluck('blocked_to')->toArray();
                    $languages  =   UserSetting::whereUserId($auth_id)->whereNotNull('language_id')->distinct()->pluck('language_id')->toArray();
                    $disLikes   =   DisLike::whereDisLikerId($auth_id)->whereDate('updated_at', \Carbon\Carbon::today())
                        ->whereNotNull('user_id')->distinct()->pluck('user_id')->toArray();

                    if (!empty($radius) && !empty($latitude) && !empty($longitude)) {
                        $users = User::select(
                            'id',
                            'custom_id',
                            'birth_date',
                            'profile_photo',
                            'gender',
                            'interest',
                            'location_id',
                            'language_id',
                            'verify_status',
                            'verify_photo_status', 
                            'verify_email_send',
                            'email_verified_at',
                            'contact_verified_at',                           
                            'is_active',
                            DB::raw("3959 * 1.609344 * acos(cos(radians(" . $latitude . ")) 
                            * cos(radians(users.latitude)) 
                            * cos(radians(users.longitude) - radians(" . $longitude . ")) 
                            + sin(radians(" . $latitude . ")) 
                            * sin(radians(users.latitude))) AS distance")
                        );
                        // ->having("distance", "<=", $radius)

                        if ($user->location_id == $user->discover_location_id) {
                            $users = $users->orderBy('distance');
                        }
                    } else {
                        $users = User::select(
                            'id',
                            'custom_id',
                            'birth_date',
                            'profile_photo',
                            'gender',
                            'verify_photo_status', 
                            'verify_email_send',
                            'email_verified_at',
                            'contact_verified_at',     
                            'interest',
                            'location_id',
                            'language_id',
                            'verify_status',
                            'is_active'
                        );
                    }

                    $users = $users->with(['userDetails', 'interests.interest.interestTranslation', 'userTranslation', 'location.locationTranslation'])
                        ->where('id', '!=', $auth_id)
                        ->whereNotNull('profile_photo')
                        ->whereIsActive('y');

                        if ($auth_interest != 'Both') {
                            $users->where('gender', $auth_interest);
                        }     // Interested in Gender

                        if (!empty($user->discover_location_id)) {                                    
                            if ($user->location_id != $user->discover_location_id) {
                                $users->where('location_id', $user->discover_location_id);  // Location
                            }
                        }

                        if (count($disLikes) > 0) {
                            $users->whereNotIn('id', $disLikes);    // Restirct DisLiked Profile
                        }                                           

                        $users->doesnthave('blockedTos');

                        // if (count($blocked) > 0) {
                        //     $users->whereNotIn('id', $blocked);     // Restirct Blocked Profile
                        // }                                           

                        // Discovery
                        if (!empty($user->discover_start_age) && !empty($user->discover_end_age)) {
                            // $users->whereBetween('birth_date', array($user->discover_start_age, $user->discover_end_age)); // Age
                            $users->whereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'),array($user->discover_start_age,$user->discover_end_age));
                        }

                        $users->where(function ($query)  use ($languages) {
                            if (count($languages) > 0) {
                                $query->orWhereIn('language_id', $languages);   // Languages
                            }       
                        });

                    $count = $users->count();
                    $users = $users->limit($request->limit ?? config('utility.pagination.limit'))
                        ->offset($request->offset ?? config('utility.pagination.offset'))
                        ->get();

                    if ($users->isNotEmpty()) {
                        return (HomeResource::collection($users))->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'is_swipe_allow'    =>  $is_swipe_allow,
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
                $this->storeErrorLog($e, 'get_home_feed');
            }
        }
        return $this->returnResponse();
    }
}
