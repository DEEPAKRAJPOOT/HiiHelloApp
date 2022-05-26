<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Resources\v1\ { HomeResource };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Models\ { User };

class HomeController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get All Users List
    public function getUsersList(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                $auth_id = $user ? $user->id : NULL;

                $users = User::select('id','custom_id','full_name','birth_date','profile_photo','gender','interest',
                                'location_id','verify_status','is_active')
                                ->with(['interests','interests.interest.interestTranslation','location.locationTranslation'])
                                ->where('id','!=',$auth_id)->whereIsActive('y');

                if(!empty($user->interest)){
                    $user_interest = $user->interest;
                    if($user_interest != 'Both'){ $users = $users->whereGender($user_interest); }
                }

                $users = $users->inRandomOrder();
                $count = $users->count();
                $users = $users->limit($request->limit ?? config('utility.pagination.limit'))
                                ->offset($request->offset ?? config('utility.pagination.offset'))
                                ->get();

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
