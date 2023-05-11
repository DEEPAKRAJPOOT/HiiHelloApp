<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request,Response};
use App\Http\Requests\Api\College\{AddCollegeRequest,CollegeListRequest};
use App\Http\Resources\v1\CollegeResource;
use App\Models\College;
use Exception;

class CollegeController extends Controller
{
    private $version = 'v.1.0';
    public function getVersion(){
        return $this->version;
    }

    public function getStatesList(Request $request){
        try {
            $search = $request->search;
            $states_list = College::select('state')->whereNotNull('approved_at');
            if(!empty($search)){
                $states_list->where('state','like','%'.$search.'%');
            }
            $count = $states_list->count();
            $states_list = $states_list->groupBy('state')->orderBy('state','asc')->pluck('state');
            if (!empty($states_list)) {
                $this->status = Response::HTTP_OK;
                $this->response['data'] = $states_list;
            } else {
                $this->status = Response::HTTP_NOT_FOUND;
                $this->response['meta']['message'] = trans('api.not_found', ['entity' => __('States')]);
            }
        }catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\College':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __('States')]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e, 'get_colleges_list');
        }
        return $this->returnResponse();
    }

    public function getCitiesList(Request $request){
        try {
            $state = $request->state;
            $search = $request->search;
            $cities_list = College::select('district')->whereNotNull('approved_at');
            if(!empty($state)){
                $cities_list->where('state',$state);
            }
            if(!empty($search)){
                $cities_list->where('district','like','%'.$search.'%');
            }
            $count = $cities_list->count();
            $cities_list = $cities_list->groupBy('district')->orderBy('district','asc');
            if(!empty($request->limit)){
                $cities_list = $cities_list->limit($request->limit);
            }
            if(!empty($request->offset)){
                $cities_list = $cities_list->offset($request->offset);
            }
            $cities_list = $cities_list->pluck('district');
            if (!empty($cities_list)) {
                $this->status = Response::HTTP_OK;
                $this->response['data'] = $cities_list;
            } else {
                $this->status = Response::HTTP_NOT_FOUND;
                $this->response['meta']['message'] = trans('api.not_found', ['entity' => __('Cities')]);
            }
        }catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\College':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __('Cities')]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e, 'get_colleges_list');
        }
        return $this->returnResponse();
    }

    public function getCollegesList(Request $request)
    {
        $collegeListRequest = new CollegeListRequest();
        if ($this->apiValidator($request->all(), $collegeListRequest->rules())) {
            try {
                $search = $request->search;
                $city = $request->city;
                $state = $request->state;
                $college_list = College::whereNotNull('approved_at');

                if(!empty($search)){
                    $college_list->where(function($query)use($search){
                        $query->where('name','like',"%{$search}%");
                        $query->orWhere('university','like',"%{$search}%");
                        $query->orWhere('abbreviation','like','%'.preg_replace('/[^a-z]/i','',$search).'%');
                    });
                }
                if(!empty($city)){
                    $college_list->where('district',$city);
                }elseif(!empty($state)){
                    $college_list->where('state',$state);
                }
                $count = $college_list->count();
                $college_list = $college_list->orderBy('name','asc')->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                if ($college_list->isNotEmpty()) {
                    return (CollegeResource::collection($college_list))
                        ->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.list', ['entity' => __('Colleges')]),
                            ]
                        ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Colleges')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\College':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __('Colleges')]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_colleges_list');
            }
        }
        return $this->returnResponse();
    }

    public function addNew(Request $request){
        $addCollegeRequest = new AddCollegeRequest();
        if($this->apiValidator($request->all(), $addCollegeRequest->rules())){
            try {
                $user = $request->user();
                $college = College::create([
                    'custom_id' => getUniqueString('colleges'),
                    'name' => $request->name,
                    'university' => $request->university,
                    'district' => $request->district,
                    'state' => $request->state,
                    'abbreviation' => preg_replace('/[^a-z]/i','',$request->abbreviation ?? ''),
                    'approved_at' => null
                ]);
                if($college){
                    $this->status = Response::HTTP_OK;
                    return (new CollegeResource($college))
                    ->additional([
                        'meta' => [
                            'is_ban'    =>  false,
                            'is_subscribed' => $user->is_subscribed === 'y',
                            'subscription_end_date' => $user->subscription_end_date
                        ]
                    ]);
                } else {
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    $this->response['meta']['is_ban'] = false;
                }
            }catch(Exception $e){
                $this->storeErrorLog($e, 'add_college');
            }
        }
        return $this->returnResponse();
    }

}