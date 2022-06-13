<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\BlockUser;
use Tests\InitialiseUserTrait;

class BlockTest extends TestCase
{
    use InitialiseUserTrait;

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

    public function test_block_list_successfully()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);
        $block_user = BlockUser::whereBlockBy($user)->latest();
        $this->postJson(route('api.user.block-list'),[])
        // ->assertStatus(404)
        ->assertJsonStructure([
            'meta' => [ 'api','url','message' ],
        ]);
    }

    public function test_block_unblock_validation()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);

        $this->postJson(route('api.user.block-unblock'))
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    public function test_block_unblock_successfully()
    {
        // $user = User::firstOrFail();
        $user = $this->createUser();
        $this->setUserToken($user);
        $data = [
            'user_id' => $user->custom_id,
            'status' => 'unblock',
        ];
        $this->postJson(route('api.user.block-unblock'),$data)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }
}
