<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use App\Http\Requests\Api\User\ { BlockUnblockRequest };
use App\Http\Requests\Api\General\ { PaginationRequest };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use App\Http\Resources\v1\ { BlockProfileResource };
use Illuminate\Support\Facades\ { Auth, DB };
use App\Models\ { User, BlockUser, ChatRoom };

class BlockController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    /**
     * Used to block/unblock any user's profile.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function blockUnblockProfile(Request $request)
    {
        $rules = BlockUnblockRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            DB::beginTransaction();
            try{
                /* Block Profile */
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $block_user = User::select('id')->whereCustomId($request->user_id)->firstOrFail();

                $chat_room = ChatRoom::where(function ($query) use ($auth_id,$block_user) {
                                    $query->whereCreatorId($auth_id)->where('participate_id',$block_user->id);
                                })->orWhere(function ($query) use ($auth_id,$block_user) {
                                    $query->whereCreatorId($block_user->id)->where('participate_id',$auth_id);
                                })->first();

                if($request->status == 'block'){
                    $block_profile = BlockUser::firstOrCreate([
                        'block_by'      =>  $auth_id,
                        'blocked_to'    =>  $block_user->id ?? NULL,
                    ],[ 
                        'custom_id'     =>  getUniqueString('block_users'),
                    ]);

                    // Block Chat
                    if($chat_room){ if(empty($chat_room->block_by)){ $chat_room->block_by = $auth_id; $chat_room->save(); } }

                    DB::commit();
                    if($block_profile->save()){
                        $this->status = Response::HTTP_OK;
                        return (['data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.block.success'),
                            ] ]);
                    }else{
                        $this->response['meta']['message']  =   rans('api.block.fail');
                        $this->status = Response::HTTP_NOT_FOUND; 
                    }
                }
                /* Unblock Profile */
                elseif($request->status == 'unblock'){
                    $block_profile = BlockUser::whereBlockBy($auth_id)->whereBlockedTo($block_user->id)->firstOrFail();
                    $unblock = $block_profile->delete();

                    // Unblock Chat
                    if($chat_room){ if($chat_room->block_by == $auth_id){ $chat_room->block_by = NULL; $chat_room->save(); } }

                    DB::commit();
                    if($unblock){
                        $this->status = Response::HTTP_OK;
                        return (['data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.unblock.success'),
                            ] ]);
                    }else{
                        $this->response['meta']['message']  =   rans('api.unblock.fail');
                        $this->status = Response::HTTP_NOT_FOUND; 
                    }
                }
            } catch(ModelNotFoundException $exception) {   
                DB::rollback();             
                switch ($exception->getModel()) {
                    case 'App\Models\BlockUser':
                        $this->response['meta']['message'] = trans('api.block.not_able');
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.block.not_able');
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                DB::rollback();
                $this->storeErrorLog($e,'block_unblock_profile');
            }
        }
        return $this->returnResponse();
    }

    /**
     * get list of blocked user's.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function blockList(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $auth_id = $request->user() ? $request->user()->id : NULL;

                $block_profiles = BlockUser::
                                    with(['blockedTo:id,custom_id,profile_photo,birth_date,location_id',
                                        'blockedTo.userTranslation', 'blockedTo.location.locationTranslation'])
                                    ->whereHas('blockedTo')
                                    ->whereBlockBy($auth_id)->latest();
                $count = $block_profiles->count();
                $block_profiles = $block_profiles->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();

                if($block_profiles->isNotEmpty()){
                    return (BlockProfileResource::collection($block_profiles))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Block list')]),
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Block list')]); 
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\BlockUser':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Block list")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'block_list');
            }
        }
        return $this->returnResponse();
    }
}
