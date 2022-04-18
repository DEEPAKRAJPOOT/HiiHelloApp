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

        $this->truncateTables($this->usersTable);
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

    // public function test_user_cannot_create_room_with_itself_validation()
    // {
    //     $user = $this->createUser();
    //     $this->setUserToken($user);

    //     $this->postJson(route('chat.create-room'),[])
    //     ->assertStatus(412)
    //     ->assertJsonStructure([
    //         'data', 'meta' => [ 'api','url','message' ],
    //     ])->assertJson([
    //         'data'  =>  NULL,
    //         'meta'  =>  [
    //             'api'       =>  $this->getVersion(),
    //             'url'       =>  url()->current(),
    //             'language'  =>  config('utility.default_lang_code'),
    //             'message'   =>  trans('validation.required', ['attribute' => __('participant_id') ])
    //         ],
    //     ]);
    // }
}
