<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Auth };
use App\Http\Requests\Api\User\ { AddLikeRequest };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Http\Resources\v1\ { LikeResource };
use App\Models\ { Like, User };

class LikeController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Add New Like
    public function addNewLike(Request $request)
    {
        $rules = AddLikeRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = User::whereCustomId($request->user_id)->whereIsActive('y')->firstOrFail();
                $like = Like::updateOrCreate([
                    'user_id'       =>  $user->id,
                    'liker_id'      =>  Auth::id(),
                ],[
                    'custom_id'     =>  getUniqueString('likes'),
                ]);

                if($like->save()){
                    $this->status = Response::HTTP_OK;
                    return (new LikeResource($like))
                        ->additional([
                            'meta' => [
                                'message'   =>  trans('api.liked', ['entity' => __("User") ]),
                            ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('User')]); 
                    $this->status = Response::HTTP_NOT_FOUND; 
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    case 'App\Models\Like':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'add_like');
            }
        }
        return $this->returnResponse();
    }

    // Get User Liked Profiles
    public function getLikes(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $likes = Like::with(['likerUser.location.locationTranslation'])
                                ->whereHas('likerUser')
                                ->where('user_id',Auth::id())
                                ->latest();
                $count = $likes->count();
                $likes = $likes->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();

                if($likes->isNotEmpty()){
                    return (LikeResource::collection($likes))
                        ->additional([
                            'meta' => [
                                'offset'        =>  $request->offset,
                                'limit'         =>  $request->limit,
                                'total'         =>  $count,
                                'api'           =>  'v.1.0',
                                'url'           =>  url()->current(),
                                'language'      =>  app()->getLocale(),
                                'message'       =>  trans('api.list',['entity' => __("Users")]),
                            ] ]);     
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __("Users")]);   
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                        break;
                    case 'App\Models\Like':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Users")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_likes');
            }
        }
        return $this->returnResponse();
    }
}
