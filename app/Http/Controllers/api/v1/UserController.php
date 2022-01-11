<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\v1\UserProfile;
use App\Http\Requests\Api\User\ProfileRequest;
use App\Http\Requests\Api\User\UserListRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;

class UserController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get User Profile
    public function getProfile(Request $request)
    {
        $rules = ProfileRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = User::whereCustomId($request->id)->whereIsActive('y')->firstOrFail();
                return (new UserProfile($user))
                            ->additional([
                            'meta' => [
                                'message'  =>  trans('api.success', ['entity' => __("User")]),
                            ] ]);

            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            }
        }
        return $this->returnResponse();
    }

    // Get All Users List With Filters
    public function getUsersList(Request $request)
    {
        $rules = UserListRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $users = User::where('id','!=',Auth::id())->whereIsActive('y');

                if(!empty($request->start_age) && !empty($request->end_age)){
                    $from   =   \Carbon\Carbon::today()->subYears($request->start_age);
                    $to     =   \Carbon\Carbon::today()->subYears($request->end_age);
                    $users  =   $users->whereBetween('birth_date',[$to, $from]);
                }
                if(!empty($request->gender)){ $users = $users->whereGender($request->gender); }
                if(!empty($request->interests)){
                    $interests = $request->interests;
                    $users = $users->whereHas('interests.interest',function($q) use ($interests){
                                $q->whereIn('custom_id',$interests);
                            });
                }
                if(!empty($request->location)){
                    $location = Location::whereCustomId($request->location)->whereIsActive('y')->firstOrFail();
                    $users = $users->whereLocationId($location->id);
                }

                $users = $users->inRandomOrder();
                $count = $users->count();
                $users = $users->limit($request->limit ?? config('utility.pagination.limit'))
                                ->offset($request->offset ?? config('utility.pagination.offset'))
                                ->get();
                if($users->isNotEmpty()){
                    return (UserProfile::collection($users))->additional([
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
                    $this->status = $this->statusArr['not_found'];     
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
            }
        }
        return $this->returnResponse();
    }

    // Get Common Age Of All Users
    public function getCommonAge()
    {
        try{
            $common_age = User::select(DB::raw('MAX(birth_date) as max_date'), DB::raw('MIN(birth_date) as min_date'))
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
                $this->status = $this->statusArr['not_found'];     
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
        }
        return $this->returnResponse();
    }
}
