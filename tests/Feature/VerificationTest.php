<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tests\InitialiseUserTrait;
use Illuminate\Http\UploadedFile;

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

    public function test_upload_verify_detail_successfully()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);
        $data = [
            'type' => 'image',
            'file' => UploadedFile::fake()->image('org_1.jpg'),
        ];
        $this->postJson(route('api.verify.upload-detail'),$data)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    public function test_verify_contact_validation()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);

        $this->postJson(route('api.verify.contact-no'))
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    // public function test_verify_contact_successfully()
    // {
    //     $user = User::firstOrFail();
    //     $this->setUserToken($user);
    //     $data = [
    //         'country_code' => '93',
    //         'contact_no' => $user->contact_no,
    //     ];
    //     dd($data);
    //     dd($this->postJson(route('api.verify.contact-no'),$data));
    //     // ->assertStatus(200)
    //     // ->assertJsonStructure([
    //     //     'data', 'meta' => [ 'api','url','message' ],
    //     // ]);
    // }

    public function test_verify_email_validation()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);

        $this->postJson(route('api.verify.verify-email'))
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    public function test_verify_email_successfully()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);
        $data = [
            'email' => $user->email,
        ];
        $this->postJson(route('api.verify.verify-email'),$data)
        // ->assertStatus(200)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }

    public function test_verify_details_successfully()
    {
        $user = User::firstOrFail();
        $this->setUserToken($user);
        $this->postJson(route('api.verify.get-details'))
        ->assertStatus(200)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ]);
    }
}
