<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\v1\UserProfile;
use App\Http\Resources\v1\ProfileReportResource;
use App\Http\Requests\Api\User\ProfileRequest;
use App\Http\Requests\Api\User\UserListRequest;
use App\Http\Requests\Api\User\ProfileReportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Location;
use App\Models\ProfileReport;

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
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_profile');
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
                if(!empty($request->languages)){
                    $languages = $request->languages;
                    $users = $users->whereHas('language',function($q) use ($languages){
                                $q->whereIn('custom_id',$languages);
                            });
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
                $this->storeErrorLog($e,'get_users_list');
            }
        }
        return $this->returnResponse();
    }

    // Store Profile Report Details
    public function storeProfileReport(Request $request)
    {
        $rules = ProfileReportRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $path = NULL;
                $reported_user = User::whereCustomId($request->reported_user)->whereIsActive('y')->firstOrFail();
                if(!empty($request->image)){
                    $path = $request->file('image')->store('profile_report');
                }

                $profile_report = ProfileReport::firstOrCreate([
                    'user_id'           =>  Auth::id(),
                    'reported_user_id'  =>  $reported_user->id,
                    'message'           =>  $request->message,
                ],[
                    'custom_id'         =>  getUniqueString('profile_reports'),
                    'image'             =>  $path,
                ]);

                if($profile_report->save()){
                    return (new ProfileReportResource($profile_report))
                            ->additional([
                            'meta' => [
                                'message'  =>  trans('api.report.success'),
                            ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.report.fail'); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'store_profile_report');
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
            $this->storeErrorLog($e,'get_common_age');
        }
        return $this->returnResponse();
    }
}
