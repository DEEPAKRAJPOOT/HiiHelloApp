<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tests\InitialiseUserTrait;

class DiscoveryTest extends TestCase
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

    public function test_set_discovery_detail_validation()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);

        $this->postJson(route('api.discovery.set-detail'))
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    public function test_set_discovery_detail_successfully()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);
        $data = [
            'distance' => 100,
            'start_age' => 20,
            'end_age' => 25,
            'interest' => 'Female',
            'location' => 'IpQiFcIwk7Qo3x79FzlF',
            'languages[0]' => 'en',
            'languages[1]' => 'hi',
        ];
        $this->postJson(route('api.discovery.set-detail'),$data)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    // public function test_get_discovery_detail_successfully()
    // {
    //     $user = User::firstOrFail();
    //     $this->setUserToken($user);
    //     $this->postJson(route('api.discovery.set-detail'))
    //     ->assertStatus(200)
    //     ->assertJsonStructure([
    //         'data', 'meta' => [ 'api','url','message' ],
    //     ]);
    // }
}
