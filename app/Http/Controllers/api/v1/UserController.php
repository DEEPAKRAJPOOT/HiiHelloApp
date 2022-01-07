<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\v1\UserProfile;
use App\Http\Requests\Api\User\ProfileRequest;
use App\Http\Requests\Api\User\UserListRequest;
use Illuminate\Http\Request;
use App\Models\User;

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
                $users = User::whereIsActive('y');

                if(!empty($request->gender)){
                    $users = $users->whereGender($request->gender);
                }

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
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            }
        }
        return $this->returnResponse();
    }
}
