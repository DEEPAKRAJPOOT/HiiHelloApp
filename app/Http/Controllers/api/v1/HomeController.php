<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Resources\v1\ { HomeResource };
use Illuminate\Support\Facades\ { DB };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Models\ { User, BlockUser, UserSetting, DisLike };

class HomeController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get All Users List
    public function getHomeFeeds(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;
                $max_interest = config('utility.profile.detail.max_interest') ?? 5;
                $auth_interest = $user->interest ? $user->interest : 'Both';
                // $radius = $user->discover_distance; $latitude = $user->latitude; $longitude = $user->longitude; 

                $blocked    =   BlockUser::whereBlockBy($auth_id)->whereNotNull('blocked_to')->distinct()->pluck('blocked_to')->toArray();
                $languages  =   UserSetting::whereUserId($auth_id)->whereNotNull('language_id')->distinct()->pluck('language_id')->toArray();
                $disLikes   =   DisLike::whereDisLikerId($auth_id)->whereDate('updated_at',\Carbon\Carbon::today())
                                    ->whereNotNull('user_id')->distinct()->pluck('user_id')->toArray();

                $users = User::query();

                // if(!empty($radius) && !empty($latitude) && !empty($longitude)){
                //     $users = $users->select('id','custom_id','birth_date','profile_photo','gender','interest',
                //                 'location_id','verify_status','is_active'
                //                 ,DB::raw("3959 * acos(cos(radians(" . $latitude . ")) 
                //                     * cos(radians(users.latitude)) 
                //                     * cos(radians(users.longitude) - radians(" . $longitude . ")) 
                //                     + sin(radians(" .$latitude. ")) 
                //                     * sin(radians(users.latitude))) AS distance"))
                //                 ->having("distance", "<=", $radius);
                //                 // ->orderBy('distance');
                // }else{
                    $users = $users->select('id','custom_id','birth_date','profile_photo','gender','interest',
                            'location_id','verify_status','is_active');
                // }

                $users = $users->with(['interests.interest.interestTranslation','userTranslation','location.locationTranslation'])
                            ->where('id','!=',$auth_id)
                            ->whereNotNull('profile_photo')             // Must Have Main Photo
                            ->whereNotIn('id',$disLikes)                // Restirct DisLiked Profile
                            ->whereNotIn('id',$blocked)                 // Restirct Blocked Profile
                            ->whereIsActive('y');
                           
                if($auth_interest != 'Both'){ $users = $users->where('gender',$auth_interest); } // Interested in Gender

                // Apply Discovery Detail
                /*$users = $users->where(function ($query)  use ($user, $languages) {
                        $query->orWhereIn('language_id',$languages);   // Languages
                               
                        if(!empty($user->discover_location_id)){
                            $query->orWhere('location_id',$user->discover_location_id); // Location
                        }
                        if(!empty($user->discover_start_age) && !empty($user->discover_end_age)){
                            $query->orWhereBetween('birth_date',array($user->discover_start_age,$user->discover_end_age)); // Age
                        }
                    });*/

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
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_users_list');
            }
        }
        return $this->returnResponse();
    }
}
