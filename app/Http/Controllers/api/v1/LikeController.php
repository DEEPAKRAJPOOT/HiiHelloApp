<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Auth, DB };
use App\Http\Requests\Api\User\ { AddLikeRequest, AddDislikeRequest };
use App\Http\Requests\Api\General\ { PaginationRequest };
use App\Http\Resources\v1\ { LikeResource };
use App\Models\ { Like, User, BlockUser, DisLike };
use App\Jobs\ { NotificationJob };

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
                $auth_user = $request->user(); $is_matched = false; 
                $user = User::whereCustomId($request->user_id)->where('id','!=',$auth_user->id)->whereIsActive('y')->firstOrFail();
                $auth_id = $auth_user->id; $user_id = $user->id;

                $block = BlockUser::whereBlockBy($user_id)->whereBlockedTo($auth_id)->first();
                        
                // Manage Swipes
                $auth_user->addSwipeCount();
                $is_swipe_allow = $auth_user->isSwipeAllow();
                if( $is_swipe_allow == false){ $auth_user->notifySwipeAlert(); } 

                if(!$block){
                    $like = Like::firstOrCreate([
                        'user_id'       =>  $user_id,
                        'liker_id'      =>  $auth_id,
                    ],[
                        'custom_id'     =>  getUniqueString('likes'),
                    ]);

                    // Remove From DisLikes
                    DisLike::whereUserId($user_id)->whereDisLikerId($auth_id)->delete();

                    if($like->save()){
                        if($like->wasRecentlyCreated){
                            $user->increment('like_count');

                            $matched = Like::select('id')->whereUserId($auth_id)->whereLikerId($user_id)->first();
                            if($matched){
                                $auth_user->increment('match_count');
                                $user->increment('match_count');
                                $is_matched = true;

                                $title = trans('api.notify_message.new_match.title');
                                $message = trans('api.notify_message.new_match.message');
                                $type = config('utility.notification.type.new_match');
                            }else{
                                $title = trans('api.notify_message.add_like.title');
                                $message = trans('api.notify_message.add_like.message');
                                $type = config('utility.notification.type.add_like');
                            }
                            $notification = [
                                'custom_id'     =>  getUniqueString('notifications'),
                                'key'           =>  'user_id',
                                'value'         =>  $like->liker_id,
                                'user_id'       =>  $user_id,
                                'title'         =>  $title,
                                'message'       =>  $message,
                                'image'         =>  '',
                                'type'          =>  $type,
                            ];
                                
                            // Notify
                            $notificationJob = new NotificationJob($notification, $user);
                            dispatch($notificationJob);
                        }

                        $this->status = Response::HTTP_OK;
                        return (['data'  =>  [
                                    'is_matched'        =>  $is_matched,
                                    'is_swipe_allow'    =>  $is_swipe_allow,
                                ],
                                'meta' => [
                                    'url'       =>  url()->current(),
                                    'api'       =>  $this->getVersion(),
                                    'language'  =>  app()->getLocale(),
                                    'message'   =>  trans('api.liked', ['entity' => __("User") ]),
                                ] ]);
                    }else{
                        $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('User')]); 
                        $this->status = Response::HTTP_NOT_FOUND; 
                    }
                }else{
                    $this->response['data']['is_swipe_allow']  =  $is_swipe_allow; 
                    $this->response['meta']['message']  =   trans('api.block.no_action',['entity' => __('like')]); 
                    $this->status = Response::HTTP_OK; 
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\BlockUser':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
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

    // Add New Dislike
    public function addNewDisLike(Request $request)
    {
        $rules = AddDislikeRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $auth_user = $request->user(); 
                $user = User::select('id')->whereCustomId($request->user_id)->whereIsActive('y')->firstOrFail();
                $auth_id = $auth_user->id; $user_id = $user->id;

                $block = BlockUser::whereBlockBy($user_id)->whereBlockedTo($auth_id)->first();
                
                // Manage Swipes
                $auth_user->addSwipeCount();
                $is_swipe_allow = $auth_user->isSwipeAllow();
                if( $is_swipe_allow == false){ $auth_user->notifySwipeAlert(); } 

                if(!$block){
                    $disLike = DisLike::updateOrCreate([
                        'user_id'       =>  $user_id,
                        'dis_liker_id'  =>  $auth_id,
                    ],[
                        'custom_id'     =>  getUniqueString('dis_likes'),
                    ]);

                    // Remove From Likes
                    Like::whereUserId($auth_id)->whereLikerId($user_id)
                        ->orWhere(function ($query) use ($user_id, $auth_id){
                            $query->whereUserId($user_id)
                                ->whereLikerId($auth_id);
                        })
                        ->delete();

                    if($disLike->save()){
                        $this->status = Response::HTTP_OK;
                        return (['data'  => [ 'is_swipe_allow'    =>  $is_swipe_allow ],
                                'meta' => [
                                    'url'       =>  url()->current(),
                                    'api'       =>  $this->getVersion(),
                                    'language'  =>  app()->getLocale(),
                                    'message'   =>  trans('api.dis-liked', ['entity' => __("User") ]),
                                ] ]);
                    }else{
                        $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('User')]); 
                        $this->status = Response::HTTP_NOT_FOUND; 
                    }
                }else{
                    $this->response['data']['is_swipe_allow']  =  $is_swipe_allow; 
                    $this->response['meta']['message']  =   trans('api.block.no_action',['entity' => __('Dis-liked')]); 
                    $this->status = Response::HTTP_OK; 
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\BlockUser':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    case 'App\Models\DisLike':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'add_dis_like');
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
                $user = $request->user(); $user_id = $user->id;
                $user->like_count = 0; // Reset Like Count
                $user->save();

                $match_users = DB::table('likes')
                    ->join("likes as like", function($q){
                        $q->on("likes.liker_id", "=", "like.user_id");
                        $q->on("like.liker_id", "=", "likes.user_id");
                    })
                    ->join('users', function($q){
                        $q->on('users.id',"=", "likes.user_id");
                    })
                    //to only get users details who likes current user
                    ->where("likes.liker_id", '=', $user_id)
                    ->where("likes.user_id", '!=', $user_id)
                    ->pluck('users.id')->toArray();

                $likes = Like::with(['likerUser:id,custom_id,birth_date,profile_photo,location_id,is_active',
                                    'likerUser.userTranslation','likerUser.location.locationTranslation'])
                                ->whereHas('likerUser')
                                ->where('user_id',$user_id)
                                ->whereNotIn('liker_id',$match_users)
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
                                'api'           =>  $this->getVersion(),
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
