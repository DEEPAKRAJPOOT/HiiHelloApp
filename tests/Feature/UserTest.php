<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\InitialiseUserTrait;
use Tests\TestCase;

class UserTest extends TestCase
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

    /* ------------------------------------------ User Profile ------------------------------------------  */

    public function test_get_profile_validation()
    {
        $data = [
            'id'      =>  'DemoIdTest',
        ];
        $this->postJson(route('api.user.get-profile'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
            'data'
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.in', ['attribute' => __('id') ])
            ],
            'data' => NULL
        ]);
    }

    public function test_get_profile_successfully()
    {        
        $user = $this->createUser();
        $data = [
            'id'    =>  $user->custom_id,
        ];
        $this->postJson(route('api.user.get-profile'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
            'data'  =>  [
                'id', 'first_name', 'last_name', 'email', 'contact' => ['code', 'number'], 'age', 'gender', 'interest', 'profile_photo', 'media' => ['profile_images', 'profile_videos'], 'flags' => [ 'profile_setuped', 'verified_staus', 'likes', 'matches', 'chats'],
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.success', ['entity' => __('User')]),
            ],
            'data'  =>  [
                'id'                =>  $user->custom_id ?? "",
                'first_name'        =>  $user->first_name ?? "",
                'last_name'         =>  $user->last_name ?? "",
                'email'             =>  $user->email ?? "",
                'contact'       =>  [
                    'code'      =>  $user->country_code,
                    'number'    =>  $user->contact_no,
                ],
                'age'               =>  $user->getAge(),
                'gender'            =>  $user->gender ?? "",
                'interest'          =>  $user->interest ?? "",
                'profile_photo'     =>  generateURL($user->profile_photo) ?? "",
                'media' =>  [
                    'profile_images'    =>  $user->getProfileImages(),
                    'profile_videos'    =>  $user->getProfileVideos(),
                ],
                'flags'             =>  [
                    'profile_setuped'       =>  $user->isProfileSetuped(),
                    'verified_staus'        =>  $user->getVerifiedStatus(),
                    'likes'                 =>  $user->countLikes(),
                    'matches'               =>  $user->countMatches(),
                    'chats'                 =>  $user->countChats(),
                ],
            ],
        ]);
    }

    public function test_users_common_age_successfully()
    {        
        $user = $this->createUser();
        
        $this->postJson(route('api.user.common-age'),[])
        ->assertOk()
        ->assertJsonStructure([
            'data'  =>  [
                'max_date', 'min_date'
            ],
            'meta' => [
                'url','api','language','message'
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Users Age')]),
            ],
        ]);
    }
}
