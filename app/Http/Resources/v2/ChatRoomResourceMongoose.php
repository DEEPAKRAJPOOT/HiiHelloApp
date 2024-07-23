<?php

namespace App\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{ChatMessageMongoose, UsersMongoose};
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class ChatRoomResourceMongoose extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $auth_id = $request->user() ? $request->user()->id : null;

        // Convert room_id to ObjectId if necessary
        $roomId = $this->_id instanceof ObjectId ? $this->_id : new ObjectId($this->_id);

        $authLatestMessage = null;

        if ($this->_id == config('utility.chat.system_chat_room')) {
            $authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message','sender_id')
                ->with(['sender' => function ($query) {
                    $query->select('custom_id');
                }])
                ->where('room_id', $roomId)
                ->where('receiver_id', $auth_id)
                ->orderBy('_id', 'desc')
                ->first();
        } elseif ($this->participate_id == $auth_id) {
            $authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message','sender_id')
                ->with(['sender' => function ($query) {
                    $query->select('custom_id');
                }])
                ->withTrashed()
                ->where('room_id', $roomId)
                ->where(function ($query) {
                    $query->where('is_vanished', true);
                    // $query->orWhere('status', '!=', 'read');
                })
                ->where(function ($query) use ($auth_id) {
                    $query->whereNull('sender_deleted_at');
                    $query->orWhere('sender_id', '!=', $auth_id);
                })
                ->orWhere(function ($query) {
                    $query->where('is_vanished', false);
                    // $query->orWhere('status', '!=', 'read');
                })
                ->orderBy('_id', 'desc')
                ->first();
        } elseif ($this->creator_id == $auth_id) {
            $authLatestMessage = ChatMessageMongoose::select('custom_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'expired_at', 'is_vanished', 'message','sender_id')
                ->with(['sender' => function ($query) {
                    $query->select('custom_id');
                }])
                ->withTrashed()
                ->where('room_id', $roomId)
                ->where(function ($query) {
                    $query->where('is_vanished', true);
                    // $query->orWhere('status', '!=', 'read');
                })
                ->where(function ($query) use ($auth_id) {
                    $query->whereNull('sender_deleted_at');
                    $query->orWhere('sender_id', '!=', $auth_id);
                })
                ->orWhere(function ($query) {
                    $query->where('is_vanished', false);
                    // $query->orWhere('status', '!=', 'read');
                })
                ->orderBy('_id', 'desc')
                ->first();
        }

        $lastMessageTimestamp = $this->convertTimeZone($authLatestMessage);
        // dd($this->participate_id);
        return [
            'id'            =>  $this->_id,
            'is_active'     =>  $this->is_active ? $this->is_active == 'y' ? true : false : false,
            'is_blocked'    =>  (isset($this->block_by_count) && !empty($this->block_by_count)) ? $this->block_by_count : false,
            'creator'  =>  $this->transformUser($this->creator_id ?? null),
            'participator'  =>  $this->transformUser($this->participate_id ?? null),
            'latest_message'    =>  $authLatestMessage ? [
                'id'        =>  $authLatestMessage->custom_id ?? '',
                'message'   =>  $authLatestMessage->getMessage() ?? null,
                'status'    =>  strtr($authLatestMessage->status ?? '', ['send' => 'sent', 'read' => 'seen']),
                'sender'  =>  [
                    'id'    =>  $this->getSender($authLatestMessage->sender_id) ?? '',
                ],
                'chat_messages_count'   =>  $this->chat_messages_count ?? 0,
                'created_at'  =>  $authLatestMessage->created_at ?? '',
                'updated_at'  =>  $authLatestMessage->updated_at ?? '',
                'deleted_at'  =>  $authLatestMessage->deleted_at ?? '',
                'expired_at'  =>  $authLatestMessage->expired_at ?? '',
                'is_vanished'  =>  (($authLatestMessage->is_vanished ?? 'n') == 'y')
            ] : null,
            'is_system_room' =>  ($this->_id == config('utility.chat.system_chat_room')),
            'vanish_mode'    =>  (($this->vanish_mode ?? 'n') == 'y'),
            'disappear_mode' =>  $this->disappear_mode ?? 'off',
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api'           =>  'v.1.0',
                'url'           =>  url()->current(),
                'language'      =>  app()->getLocale(),
            ],
        ];
    }

    private function transformUser($user_id)
    {
        // dd($user_id);
        $user = UsersMongoose::where('user_id',$user_id)->first();
        if (!$user || empty($user)) {
            return [
                'id' => '',
                'full_name' => '',
                'profile' => '',
                'language' => ['lang_code' => ''],
            ];
        }

        // Ensure $user is an array and has the first element
        $userData = is_array($user) && isset($user[0]) ? $user[0] : $user;

        return [
            'id'            =>  $userData['custom_id'] ?? "",
            'full_name'     =>  $userData['full_name'] ?? "",
            'profile'       =>  $userData['profile_photo'] ?? "",
            'language'      =>  [
                'lang_code' =>  $userData['lang_code'] ?? "",
            ],
        ];
    }

    public function convertTimeZone($document) {
        // Ensure $document is an object and contains created_at and updated_at
        if (is_object($document) && isset($document->created_at) && isset($document->updated_at)) {
            // Check if created_at and updated_at are instances of MongoDB\BSON\UTCDateTime
            if ($document->created_at instanceof UTCDateTime) {
                $created_at = $document->created_at->toDateTime();
            } else {
                $created_at = null;
            }

            if ($document->updated_at instanceof UTCDateTime) {
                $updated_at = $document->updated_at->toDateTime();
            } else {
                $updated_at = null;
            }

            // Convert to Carbon instance and set to IST if dates are valid
            if ($created_at && $updated_at) {
                $created_at_ist = Carbon::parse($created_at)->setTimezone('Asia/Kolkata');
                $updated_at_ist = Carbon::parse($updated_at)->setTimezone('Asia/Kolkata');
                
                // Format the date
                $created_at_formatted = $created_at_ist->format('Y-m-d H:i:s');
                $updated_at_formatted = $updated_at_ist->format('Y-m-d H:i:s');
                
                return ['created_at' => $created_at_formatted, 'updated_at' => $updated_at_formatted];
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function getSender($sender_id)
    {
        if (!is_null($sender_id)) {
            $senderData = UsersMongoose::select('custom_id')->where('user_id', $sender_id)->first();
            if (!empty($senderData)) {
                return $senderData->custom_id;
            }
        }

        return "";
    }
}
