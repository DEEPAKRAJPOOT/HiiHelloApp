<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\InitialiseUserTrait;
use Tests\TestCase;
use App\Models\Like;
use App\Models\User;

class LikeTest extends TestCase
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

    public function test_add_like_validation()
    {
        $user = $this->createUser();
        $user->is_active = 'n';
        $user->save();
        $this->setUserToken($user);

        $data = [
            'user_id'     =>  $user->custom_id,
        ];
        $this->postJson(route('api.user.add-like'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.in', ['attribute' => __('user_id') ])
            ],
        ]);
    }

    public function test_add_like_successfully()
    {
        $first_user = $this->createUser();
        $second_user = $this->createUser();
        $this->setUserToken($first_user);

        $data = [
            'user_id'     =>  $second_user->custom_id,
        ];
        $this->postJson(route('api.user.add-like'),$data)
        // ->assertOk()
        ->assertJsonStructure([
            'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.liked', ['entity' => __("User") ]),
            ],
        ]);
    }

    public function test_get_likes_successfully()
    {
        $like = Like::latest()->firstOrFail();
        $user = User::whereId($like->user_id)->whereIsActive('y')->firstOrFail();
        $this->setUserToken($user);

        $this->postJson(route('api.user.get-likes'),[])
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list',['entity' => __("Users")]),
            ],
        ]);
    }
}
