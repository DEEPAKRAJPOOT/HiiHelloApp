<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\InitialiseUserTrait;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use InitialiseUserTrait, WithFaker;
    
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();

        // $this->truncateTables($this->usersTable);
        // $this->setAuthenticatedToken('users');
        // $this->withHeader('Authorization', 'Bearer ' . $this->token);
        $this->withHeader('Content-Type', 'application/json');
        $this->withHeader('x-language', config('utility.default_lang_code'));
    }

    public function test_create_chat_room_validation()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $this->postJson(route('chat.create-room'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('participant_id') ])
            ],
        ]);
    }

    public function test_user_cannot_create_room_with_itself_validation()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $this->postJson(route('chat.create-room'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('participant_id') ])
            ],
        ]);
    }

    public function test_create_chat_room_successfully()
    {
        $user = $this->createUser();
        $user2 = $this->createUser();
        $this->setUserToken($user);

        $data = [
            'participant_id'    =>  $user2->custom_id,
        ];
        $this->postJson(route('chat.create-room'),$data)
        // ->assertOk()
        ->assertJsonStructure([
            'meta'  =>  [ 'message' ],
        ])->assertJson([
            'meta'  =>  [
                'message'   =>  trans('api.save', ['entity' =>  __('Chat room')]),
            ],
        ]);
    }

    public function test_get_chat_rooms_when_user_not_login_validation()
    {
        $this->postJson(route('chat.get-rooms'),[])
        ->assertStatus(401)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'message'   =>  __("Unauthenticated.")
            ],
        ]);
    }

    public function test_get_chat_rooms_successfully()
    {
        $this->createUser();
        $user2 = $this->createUser();

        $user = $this->getChatRoomUser();
        $this->setUserToken($user);

        $chat_room = $this->getChatRoom();
        $this->storeNewChatMessage($chat_room);

        $this->postJson(route('chat.get-rooms'),[])
        ->assertOk()
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' =>  __('Chat rooms')]),
            ],
        ]);
    }

    public function test_get_chat_room_messages_when_user_not_login_validation()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $this->postJson(route('chat.get-messages'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'message'   => trans('validation.required', ['attribute' =>  __('room')]),
            ],
        ]);
    }

    public function test_get_chat_room_messages_successfully()
    {
        $user = $this->getChatRoomUser();
        $this->setUserToken($user);

        $chat_room = $this->getChatRoom();
        $this->storeNewChatMessage($chat_room);

        $data = [
            'room'      =>  $chat_room->custom_id,
            'limit'     =>  10,
            'offset'    =>  0,
        ];

        $this->postJson(route('chat.get-messages'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Chat history')]),
            ],
        ]);
    }
}
