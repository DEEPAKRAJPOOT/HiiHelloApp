<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Tests\InitialiseUserTrait;

use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use App\Models\Location;
use App\Models\Interest;
use App\Models\Language;

class AuthenticationTest extends TestCase
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

    /* ------------------------------------------ Login CheckSum ------------------------------------------  */

    public function test_generate_checksum_validation()
    {
        $this->postJson(route('api.generate-checksum'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
            'data'
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  'v.1.0',
                'url'       =>  route('api.generate-checksum'),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('contact_no')])
            ],
            'data' => NULL
        ]);
    }

    public function test_generate_checksum_successfully()
    {
        $data = [
            'contact_no'    =>  123456789,
        ];
        $this->postJson(route('api.generate-checksum'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'data'  =>  [
                'checksum', 'data', 'message' 
            ],
        ])->assertJson([
            'data'  =>  [
                'message'   =>  trans('Payload generated successfully')
            ],
        ]);
    }

    /* ------------------------------------------ REGISTER ------------------------------------------  */

    public function test_set_profile_validation()
    {
        $this->postJson(route('api.user.set-profile'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
            'data'
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  'v.1.0',
                'url'       =>  route('api.user.set-profile'),
                'language'  =>  config('utility.default_lang_code'),
            ],
            'data' => NULL
        ]);
    }

    public function test_set_profile_successfully()
    {
        $user = $this->getUser();

        $this->postJson(route('api.user.set-profile'),$user)
        ->assertStatus(201)
        ->assertJsonStructure([
            'meta' => [
                'message', 'auth_token'
            ],
            'data' =>   [
                'first_name', 'last_name', 'email', 'contact', 'gender', 'interest', 'flags'
            ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  'v.1.0',
                'url'       =>  route('api.user.set-profile'),
                'message'   =>  trans('api.profile_setuped'),
            ],
            'data'  =>  [
                'first_name'    =>  $user['first_name'],
                'last_name'     =>  $user['last_name'],
                'email'         =>  $user['email'],
                'contact'       =>  [
                    'code'      =>  $user['country_code'],
                    'number'    =>  $user['contact_no'],
                ],
                'gender'        =>  $user['gender'],
                'interest'      =>  $user['interest'],
                'flags'         =>  [
                    'profile_setuped'   =>  true,
                ],
            ],
        ]);
    }
}