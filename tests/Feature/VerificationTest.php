<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tests\InitialiseUserTrait;

class VerificationTest extends TestCase
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

    public function test_upload_verify_detail_validation()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);

        $this->postJson(route('api.verify.upload-detail'))
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    // public function test_upload_verify_detail_successfully()
    // {
    //     $user = User::firstOrFail();
    //     $this->setUserToken($user);
    //     $data = [
    //         'type' => 'image',
    //         'file' => 'jpeg',
    //     ];
    //     dd($this->postJson(route('api.verify.upload-detail'),$data));
    //     // ->assertStatus(200)
    //     // ->assertJsonStructure([
    //     //     'data', 'meta' => [ 'api','url','message' ],
    //     // ]);
    // }
}
