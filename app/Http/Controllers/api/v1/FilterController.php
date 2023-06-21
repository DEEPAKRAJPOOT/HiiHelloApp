<?php

namespace App\Http\Controllers\api\v1;

use Carbon\Carbon;
use App\Models\Like;
use App\Models\DisLike;
use App\Models\ProfileReport;
use App\Models\{User, Location};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB};
use Illuminate\Http\{Request, Response};
use App\Http\Resources\v1\{HomeResource};
use App\Http\Requests\Api\User\{ProfileFilterRequest};
use Illuminate\Database\Eloquent\{ModelNotFoundException};

class FilterController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }

    // Apply Filters On Users List
    public function getUsersByFilter(Request $request)
    {
        $profileFilterRequest = new ProfileFilterRequest();
        if ($this->apiValidator($request->all(), $profileFilterRequest->rules())) {
            try {
                $user = $request->user();
                $is_swipe_allow = $user->isSwipeAllow();

                if ($is_swipe_allow) {
                    $auth_id = $user ? $user->id : NULL;
                    $auth_interest = $user->interest ? $user->interest : 'Both';
                    $radius = $user->discover_distance;
                    $latitude = $user->latitude;
                    $longitude = $user->longitude;

                    $last7thDate = (new Carbon)->subDays(7)->startOfDay();
                    $last30thDate = (new Carbon)->subDays(30)->startOfDay();
                    $currentDate = (new Carbon)->now()->endOfDay();

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
                        'is_active',
                        'trusted_score',
                        DB::raw("3959 * 1.609344 * acos(cos(radians(" . $latitude . ")) 
                                    * cos(radians(users.latitude)) 
                                    * cos(radians(users.longitude) - radians(" . $longitude . ")) 
                                    + sin(radians(" . $latitude . ")) 
                                    * sin(radians(users.latitude))) AS distance")
                    )
                        ->with([
                            'userDetails', 'interests', 'interests.interest.interestTranslation',
                            'userTranslation', 'location.locationTranslation'
                        ])
                        ->withCount('interests')
                        ->whereNotNull('profile_photo')
                        ->whereNotNull('location_id')
                        ->whereUserStatus('active')
                        ->where('id','!=',config('utility.system.system_user_id'))
                        ->where(function ($query)  use ($auth_id, $auth_interest) {
                            $query->where('id', '!=', $auth_id)->whereIsActive('y');

                            if ($auth_interest != 'Both') {
                                $query->where('gender', $auth_interest);
                            }    // Interested in Gender
                        });

                    // No of fields in advanced search
                    $filter_limit = 8;
                    $filters_applied = 0;

                    if (!empty($user->discover_location_id)) {
                        if ($user->location_id != $user->discover_location_id) {
                            $users->where('location_id', $user->discover_location_id);  // Location
                            $filter_limit = 1;
                        }
                    }

                    if (!empty($user->discover_start_age) && !empty($user->discover_end_age)) {
                        // $users->whereBetween('birth_date', array($user->discover_start_age, $user->discover_end_age)); // Age
                        $users->whereBetween(\DB::raw('TIMESTAMPDIFF(YEAR,users.birth_date,CURDATE())'), array($user->discover_start_age, $user->discover_end_age));
                    }

                    if ($user->location_id == $user->discover_location_id) {
                        $users = $users->having("distance", "<=", $radius)->orderBy('distance');
                    }


                    $users = $users->where(function ($query_filter)  use ($request,$filter_limit,$filters_applied) {
                        if (!empty($request->college) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('college', function ($query_relation) use ($request) {
                                $query_relation->where('custom_id',$request->college)->whereNotNull('approved_at');
                            });
                        }
                        if (!empty($request->relationship_status) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('relationshipStatus', function ($query_relation) use ($request) {
                                $query_relation->whereSlug($request->relationship_status)->whereIsActive('y');
                            });
                        }
                        if (!empty($request->personalities) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('personalities.personality', function ($query_personality) use ($request) {
                                $query_personality->whereIn('custom_id', $request->personalities)->whereIsActive('y');
                            });
                        }
                        if (!empty($request->star_sign) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('starSign', function ($query_star_sign) use ($request) {
                                $query_star_sign->whereSlug($request->star_sign)->whereIsActive('y');
                            });
                        }

                        if (!empty($request->community) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('community', function ($query_community) use ($request) {
                                $query_community->whereSlug($request->community)->whereIsActive('y');
                            });
                        }
                        if (!empty($request->religion) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('religion', function ($query_religion) use ($request) {
                                $query_religion->whereSlug($request->religion)->whereIsActive('y');
                            });
                        }
                        if (!empty($request->fav_movie) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $fav_movie = $request->fav_movie;
                            $query_filter->whereHas('userTranslations', function ($query_fav_movie) use ($fav_movie) {
                                $query_fav_movie->where('fav_movie', 'like', "%{$fav_movie}%");
                            });
                        }
                        if (!empty($request->interests) && $filters_applied < $filter_limit) {
                            $filters_applied++;
                            $query_filter->whereHas('interests.interest', function ($query_interests) use ($request) {
                                $query_interests->whereIn('custom_id', $request->interests)->whereIsActive('y');
                            });
                        }
                    });

                    $disLikes   =   DisLike::whereDisLikerId($auth_id)->whereBetween('updated_at', [$last7thDate, $currentDate])
                        ->whereNotNull('user_id')->distinct()->pluck('user_id')
                        ->toArray();
                    $likes   =   Like::whereLikerId($auth_id)
                        ->whereBetween('updated_at', [$last7thDate, $currentDate])
                        ->whereNotNull('user_id')->distinct()->pluck('user_id')
                        ->toArray();

                    $reported = ProfileReport::where('user_id', $auth_id)
                        ->whereBetween('updated_at', [$last30thDate, $currentDate])
                        ->whereNotNull('user_id')->distinct()->pluck('reported_user_id')
                        ->toArray();

                    if (count($disLikes) > 0) {
                        $users->whereNotIn('users.id', $disLikes);    // Restrict DisLiked Profile
                    }

                    if (count($likes) > 0) {
                        $users->whereNotIn('users.id', $likes);   // Restrict Liked Profile
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

                    $users = $users->orderBy('distance')
                        ->orderBy('last_online','DESC')
                        ->orderBy('email_verified_at', "DESC")
                        ->orderBy('contact_verified_at', "DESC")
                        ->orderBy('photo_verified_at', "DESC")
                        ->orderBy('interests_count', "DESC")
                        ->orderBy('profile_percentage', "DESC");

                    $count = $users->count();
                    $users = $users->limit($request->limit ?? config('utility.pagination.limit'))
                        ->offset($request->offset ?? config('utility.pagination.offset'))
                        ->get();
                    // return response()->json($users);
                    // echo "<pre>"; print_r($users->toArray()); die();

                    if ($users->isNotEmpty()) {
                        return (HomeResource::collection($users))->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'is_swipe_allow'    =>  $is_swipe_allow,
                                'is_profile_photo' =>  $user->profile_photo != '',
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.list', ['entity' => __('Users')]),
                            ]
                        ]);
                    } else {
                        $this->response['meta']['is_swipe_allow'] = $is_swipe_allow;
                        $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Users')]);
                        $this->status = Response::HTTP_NOT_FOUND;
                    }
                } else {
                    $this->response['meta']['is_swipe_allow'] = $is_swipe_allow;
                    $this->response['meta']['message']  =   trans('api.swipe_over');
                    $this->response['meta']['is_ban'] = false;
                    $this->status = Response::HTTP_FORBIDDEN;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
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
                $this->storeErrorLog($e, 'get_users_filters');
            }
        }
        return $this->returnResponse();
    }
}
