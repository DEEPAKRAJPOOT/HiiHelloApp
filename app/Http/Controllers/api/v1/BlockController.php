<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use App\Http\Requests\Api\User\{BlockUnblockRequest};
use App\Http\Requests\Api\General\{PaginationRequest};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Resources\v1\{BlockProfileResource};
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{User, BlockUser, ChatRoom, ChatMessageMongoose, ChatRoomMongoose, UsersMongoose};
// use App\Models\{ChatRoom, ChatMessage, User, CallLog,ChatMessageMongoose, ChatRoomMongoose, UsersMongoose};

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
        $blockUnblockRequest = new BlockUnblockRequest();
        if ($this->apiValidator($request->all(), $blockUnblockRequest->rules())) {
            DB::beginTransaction();
            try {
                /* Block Profile */
                $auth_id = $request->user() ? $request->user()->id : NULL;
                $block_user = User::select('id')->whereCustomId($request->user_id)->where('id','!=',config('utility.system.system_user_id'))->firstOrFail();

                $chat_room = ChatRoomMongoose::where(function ($query) use ($auth_id, $block_user) {
                    $query->where('creator_id',$auth_id)->where('participate_id', $block_user->id);
                })->orWhere(function ($query) use ($auth_id, $block_user) {
                    $query->where('creator_id',$block_user->id)->where('participate_id', $auth_id);
                })->first();

                if ($request->status == 'block') {
                    $block_profile = BlockUser::firstOrCreate([
                        'block_by'      =>  $auth_id,
                        'blocked_to'    =>  $block_user->id ?? NULL,
                        'block_type'    =>  'block'
                    ], [
                        'custom_id'     =>  getUniqueString('block_users'),
                    ]);

                    // Block Chat
                    if ($chat_room) {
                        if (empty($chat_room->block_by)) {
                            $chat_room->block_by = $auth_id;
                            $chat_room->save();
                        }
                    }

                    DB::commit();
                    if ($block_profile->save()) {
                        $this->status = Response::HTTP_OK;
                        return ([
                            'data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.block.success'),
                            ]
                        ]);
                    } else {
                        $this->response['meta']['message']  =  trans('api.block.fail');
                        $this->response['meta']['is_ban'] = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                    }
                }
                /* Unblock Profile */
                elseif ($request->status == 'unblock') {
                    $block_profile = BlockUser::blockedOnly()->whereBlockBy($auth_id)->whereBlockedTo($block_user->id)->firstOrFail();
                    $unblock = $block_profile->delete();

                    // Unblock Chat
                    if ($chat_room) {
                        if ($chat_room->block_by == $auth_id) {
                            $chat_room->block_by = NULL;
                            $chat_room->save();
                        }
                    }

                    DB::commit();
                    if ($unblock) {
                        $this->status = Response::HTTP_OK;
                        return ([
                            'data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.unblock.success'),
                            ]
                        ]);
                    } else {
                        $this->response['meta']['message']  =   trans('api.unblock.fail');
                        $this->response['meta']['is_ban'] = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                    }
                }
                elseif ($request->status == 'hide') {
                    $hide_profile = BlockUser::firstOrCreate([
                        'block_by'      =>  $auth_id,
                        'blocked_to'    =>  $block_user->id ?? NULL,
                        'block_type'    =>  'hide'
                    ], [
                        'custom_id'     =>  getUniqueString('block_users'),
                    ]);

                    DB::commit();
                    if ($hide_profile->save()) {
                        $this->status = Response::HTTP_OK;
                        return ([
                            'data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.hide.success'),
                            ]
                        ]);
                    } else {
                        $this->response['meta']['message']  =   trans('api.hide.fail');
                        $this->response['meta']['is_ban'] = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                    }
                }
                elseif ($request->status == 'unhide') {
                    $hide_profile = BlockUser::hiddenOnly()->whereBlockBy($auth_id)->whereBlockedTo($block_user->id)->firstOrFail();
                    $unhide = $hide_profile->delete();

                    DB::commit();
                    if ($unhide) {
                        $this->status = Response::HTTP_OK;
                        return ([
                            'data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'is_ban'    =>  false,
                                'message'   =>  trans('api.unhide.success'),
                            ]
                        ]);
                    } else {
                        $this->response['meta']['message']  =  trans('api.unhide.fail');
                        $this->response['meta']['is_ban'] = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                    }
                }
            } catch (ModelNotFoundException $exception) {
                DB::rollback();
                switch ($exception->getModel()) {
                    case 'App\Models\BlockUser':
                        $this->response['meta']['message'] = trans('api.block.not_able');
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.block.not_able');
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                DB::rollback();
                $this->storeErrorLog($e, 'block_unblock_profile');
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
        $paginationRequest = new PaginationRequest;
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
            try {
                $auth_id = $request->user() ? $request->user()->id : NULL;

                $block_profiles = BlockUser::with([
                        'blockedTo:id,custom_id,profile_photo,birth_date,location_id',
                        'blockedTo.userTranslation', 'blockedTo.location.locationTranslation'
                    ])
                    ->whereHas('blockedTo')
                    ->whereBlockBy($auth_id)->latest();
                $count = $block_profiles->count();
                $block_profiles = $block_profiles->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                if ($block_profiles->isNotEmpty()) {
                    return (BlockProfileResource::collection($block_profiles))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'message'   =>  trans('api.list', ['entity' => __('Block list')]),
                        ]
                    ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Block list')]);
                    $this->response['meta']['is_ban'] = false;
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\BlockUser':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Block list")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'block_list');
            }
        }
        return $this->returnResponse();
    }
}
