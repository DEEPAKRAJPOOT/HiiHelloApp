<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Support\Facades\ { DB };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Requests\Api\User\ { ProfileFilterRequest };
use App\Http\Resources\v1\ { HomeResource };
use App\Models\ { User, Location };

class FilterController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Apply Filters On Users List
    public function getUsersByFilter(Request $request)
    {
        $rules = ProfileFilterRequest::rules($request);
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;
                $max_interest = config('utility.profile.detail.max_interest') ?? 5;
                $auth_interest = $user->interest ? $user->interest : 'Both';
                $radius = $user->discover_distance; $latitude = $user->latitude; $longitude = $user->longitude; 

                $users = User::select('id','custom_id','birth_date','profile_photo','gender','interest',
                            'location_id','language_id','verify_status','is_active',
                            DB::raw("3959 * acos(cos(radians(" . $latitude . ")) 
                                    * cos(radians(users.latitude)) 
                                    * cos(radians(users.longitude) - radians(" . $longitude . ")) 
                                    + sin(radians(" .$latitude. ")) 
                                    * sin(radians(users.latitude))) AS distance"))
                            ->with(['interests','interests.interest.interestTranslation',
                                'userTranslation','location.locationTranslation'])
                            ->where(function ($query)  use ($auth_id, $auth_interest) {
                                $query->where('id','!=',$auth_id)->whereIsActive('y');

                                if($auth_interest != 'Both'){ $query->where('gender',$auth_interest); }    // Interested in Gender
                            });
                
                $users = $users->where(function ($query_filter)  use ($request) {
                    if(!empty($request->relationship_status)){
                        $query_filter->orWhereHas('relationshipStatus', function($query_relation) use ($request){
                            $query_relation->whereSlug($request->relationship_status);
                        });
                    }
                    if(!empty($request->personality)){
                        $query_filter->orWhereHas('personality', function($query_personality) use ($request){
                            $query_personality->whereCustomId($request->personality);
                        });
                    }
                    if(!empty($request->star_sign)){
                        $query_filter->orWhereHas('starSign', function($query_star_sign) use ($request){
                            $query_star_sign->whereSlug($request->star_sign);
                        });
                    }
                    if(!empty($request->fav_movie)){
                        $fav_movie = $request->fav_movie;
                        $query_filter->orWhereHas('userTranslations', function($query_fav_movie) use ($fav_movie){
                                            $query_fav_movie->where('fav_movie','like', "%{$fav_movie}%");
                                        }); 
                    }
                    if(!empty($request->interests)){
                        $query_filter->orWhereHas('interests.interest', function($query_interests) use ($request){
                            $query_interests->whereIn('custom_id',$request->interests);
                        });
                    }
                });

                // $users = $users->inRandomOrder();
                $users = $users->orderBy('distance');
                $count = $users->count();
                $users = $users->limit($request->limit ?? config('utility.pagination.limit'))
                                ->offset($request->offset ?? config('utility.pagination.offset'))
                                ->get()
                                ->map(function($map) use ($max_interest){
                                    $map['interests'] =  $map->interests->sortBy('desc')->take($max_interest);
                                    return $map;
                                });

                if($users->isNotEmpty()){
                    return (HomeResource::collection($users))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Users')]),
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Users')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                        break;
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Location")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_users_filters');
            }
        }
        return $this->returnResponse();
    }
}
