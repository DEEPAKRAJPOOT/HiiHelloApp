<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
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

                $users = User::select('id','custom_id','birth_date','profile_photo','gender','interest',
                                'location_id','verify_status','is_active')
                                ->with(['interests','interests.interest.interestTranslation',
                                    'userTranslation','location.locationTranslation'])
                                ->where('id','!=',$auth_id)->whereIsActive('y');

                if($auth_interest != 'Both'){ $users = $users->where('gender',$auth_interest); }    // Interested in Gender
                
                if(!empty($request->relationship_status)){
                    $users = $users->whereHas('relationshipStatus', function($query) use ($request){
                        $query->whereSlug($request->relationship_status);
                    });
                }
                if(!empty($request->personality)){
                    $users = $users->whereHas('personality', function($query) use ($request){
                        $query->whereCustomId($request->personality);
                    });
                }
                if(!empty($request->star_sign)){
                    $users = $users->whereHas('starSign', function($query) use ($request){
                        $query->whereSlug($request->star_sign);
                    });
                }
                if(!empty($request->fav_movie)){
                    $fav_movie = $request->fav_movie;
                    $users = $users->whereHas('userTranslations', function($query) use ($fav_movie){
                                        $query->where('fav_movie','like', "%{$fav_movie}%");
                                    }); 
                }
                if(!empty($request->interests)){
                    $users = $users->whereHas('interests.interest', function($query) use ($request){
                        $query->whereIn('custom_id',$request->interests);
                    });
                }

                $users = $users->inRandomOrder();
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
