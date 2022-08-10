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

        // $this->truncateTables($this->usersTable);
        // $this->setAuthenticatedToken('users');
        // $this->withHeader('Authorization', 'Bearer ' . $this->token);
        $this->withHeader('Content-Type', 'application/json');
        $this->withHeader('x-language', config('utility.default_lang_code'));
    }

    /* ------------------------------------------ User Profile ------------------------------------------  */

    public function test_get_profile_not_found()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $data = [
            'id'      =>  'DemoIdTest',
        ];
        $this->postJson(route('api.user.get-profile'),$data)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.not_found', ['entity' => __('User') ])
            ],
        ]);
    }

    public function test_get_profile_successfully()
    {        
        $auth_user = $this->createUser();
        $this->setUserToken($auth_user);
        $user = $this->createUser();

        $data = [
            'id'    =>  $user->custom_id,
        ];
        $this->postJson(route('api.user.get-profile'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'data'  =>  [
                'id', 'full_name', 'age', 'interests', 'extra' => ['about_me'], 'location', 'interests', 'profile_photo', 
                'my_things' =>  [
                    'relationship_status', 'i_am_here', 'food_preference', 'drinking', 'smoking', 'pet', 'star_sign', 'community'
                ],
                'my_basics' =>  [
                    'personalities', 'education', 'university_college', 'profession', 'religion'
                ],
                'media' => [
                    'profile_images', 'profile_videos',
                    'profile_voice' =>  [
                        'voice', 'voice_answer'
                    ],
                ], 'flags' => [ 'verified_staus', 'is_blocked'],
            ],
            'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.success', ['entity' => __('User')]),
            ],
            'data'  =>  [
                'id'                =>  $user->custom_id ?? "",
                'full_name'         =>  $user->userTranslation ? $user->userTranslation->full_name : "",
                'age'               =>  $user->getAge(),
                'profile_photo'     =>  generateURL($user->profile_photo) ?? "",
                'media' =>  [
                    'profile_images'    =>  $user->getProfileImages(),
                    'profile_videos'    =>  $user->getProfileVideos(),
                ],
                'flags'             =>  [
                    'is_blocked'            =>  $user->blocked_tos_count ? $this->blocked_tos_count > 0 ? true : false : false,
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
            'data'  =>  [ 'max_date', 'min_date' ],
            'meta' => [ 'url','api','language','message' ],
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
