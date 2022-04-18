<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Tests\InitialiseUserTrait;
use Tests\TestCase;

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
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
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
            'data'  =>  [ 'checksum', 'data', 'message' ],
        ])->assertJson([
            'data'  =>  [
                'message'   =>  trans('Payload generated successfully')
            ],
        ]);
    }

    public function test_one_character_contact_no_login()
    {
        $data = [
            'contact_no'    =>  1,
        ];
        $this->postJson(route('api.user.login'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.min.string', ['attribute' => __('contact_no'), 'min' => 2])
            ],
            'data' => NULL
        ]);
    }

    public function test_max_16_character_contact_no_login()
    {
        $data = [
            'contact_no'    =>  123456789123456789,
        ];
        $this->postJson(route('api.user.login'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.max.string', ['attribute' => __('contact_no'), 'max' => 16])
            ],
            'data' => NULL
        ]);
    }

    public function test_contact_no_required_when_preset_security_token_login()
    {
        $data = [
            'security_token'    =>  23423434234,
        ];
        $this->postJson(route('api.user.login'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('contact_no')])
            ],
            'data' => NULL
        ]);
    }

    /* ------------------------------------------ REGISTER ------------------------------------------  */

    public function test_set_profile_validation()
    {
        $this->postJson(route('api.user.set-profile'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
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
            'meta'  =>   [ 'message', 'auth_token' ],
            'data'  =>   [ 'full_name', 'contact', 'gender', 'interest', 'flags' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'message'   =>  trans('api.profile_setuped'),
            ],
            'data'  =>  [
                'full_name'     =>  $user['full_name'],
                'contact'       =>  [
                    'code'      =>  $user['country_code'],
                    'number'    =>  $user['contact_no'],
                ],
                'gender'        =>  $user['gender'],
                'interest'      =>  $user['interest'],
            ],
        ]);
    }


    /* ------------------------------------------ SOCIAL LOGIN ------------------------------------------  */

    public function test_social_login_validation()
    {
        $this->postJson(route('api.social-login'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('full_name')])
            ],
            'data' => NULL
        ]);
    }

    public function test_check_correct_name_type_social_login_validation()
    {
        $data = [
            'full_name'     =>  'Test Socail Login',
            'email'         =>  'testsocial@gmail.com',
            'type'          =>  'facebook',
        ];
        $this->postJson(route('api.social-login'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('facebook_id')])
            ],
        ]);
    }

    public function test_social_login_successfully()
    {
        $data = [
            'full_name'     =>  'Test Socail Login',
            'email'         =>  'testsocial@gmail.com',
            'type'          =>  'google',
            'google_id'     =>  'G12345',
        ];
        $this->postJson(route('api.social-login'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'meta'  =>  [ 'message', 'auth_token' ],
        ])->assertJson([
            'meta'  =>  [
                'message'   =>  trans('api.login')
            ],
        ]);
    }

    /* ------------------------------------------ LOGOUT ------------------------------------------  */

    public function test_unauthenticated_when_already_logout_or_token_not_available()
    {
        $this->postJson(route('api.user.logout'),[])
        ->assertStatus(401)
        ->assertJsonStructure([
            'data', 'meta'  =>  ['message'],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'message'   =>  __("Unauthenticated.")
            ],
        ]);
    }

    public function test_logout_successfully()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $this->postJson(route('api.user.logout'),[])
        ->assertOk()
        ->assertJsonStructure([
            'meta'  =>  [ 'message' ],
        ])->assertJson([
            'meta'  =>  [
                'message'   =>  trans('api.logout')
            ],
        ]);
    }

    /* ------------------------------------------ DEVICE TOKEN ------------------------------------------  */

    public function test_add_device_token_api_device_validation()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $this->postJson(route('api.user.add-device-token'),[])
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('device')])
            ],
        ]);
    }

    public function test_add_device_token_api_token_validation()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $data = [
            'device'    =>  'APP',
        ];

        $this->postJson(route('api.user.add-device-token'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.required', ['attribute' => __('token')])
            ],
        ]);
    }

    public function test_add_device_token_api_device_type_validation()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $data = [
            'device'    =>  'APP',
            'token'     =>  '45rfverf234f342rt234rt',
            'type'      =>  'iphone', // ios,android
        ];

        $this->postJson(route('api.user.add-device-token'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.in', ['attribute' => __('type')])
            ],
        ]);
    }

    public function test_add_device_token_successfully()
    {
        $user = $this->createUser();
        $this->setUserToken($user);

        $data = [
            'device'        =>  'APP',
            'token'         =>  '45rfverf234f342rt234rt',
            'type'          =>  'android', // ios,android
            'version'       =>  '0.1',
            'os'            =>  'Mac',
            'app_version'   =>  'v1'
        ];

        $this->postJson(route('api.user.add-device-token'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'meta'  =>  [ 'message' ],
        ])
        ->assertJson([
            'meta'  =>  [
                'message'   => trans('api.add', ['entity' => __('Device token')]),
            ],
        ]);
    }
}