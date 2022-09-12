<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\InitialiseUserTrait;
use Tests\TestCase;

class ReportTest extends TestCase
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

    public function test_profile_report_validation()
    {
        $user = $this->createUser();
        $user->is_active = 'n';
        $user->save();
        $this->setUserToken($user);

        $data = [
            'reported_user'     =>  $user->custom_id,
            'message'           =>  'hi',
            'image'             =>   UploadedFile::fake()->image('profile_report_1.jpg'),
        ];
        $this->postJson(route('api.user.profile-report'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'data', 'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'data'  =>  NULL,
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.min.string', ['attribute' => __('message'), 'min' => 3 ])
            ],
        ]);
    }

    public function test_profile_report_successfully()
    {
        $first_user = $this->createUser();
        $second_user = $this->createUser();
        $this->setUserToken($first_user);

        $data = [
            'reported_user'     =>  $second_user->custom_id,
            'message'           =>  'hello this is report profile test',
            'image'             =>   UploadedFile::fake()->image('profile_report_1.jpg'),
        ];
        $this->postJson(route('api.user.profile-report'),$data)
        // ->assertOk()
        ->assertJsonStructure([
            'meta' => [ 'api','url','message' ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.report.success'),
            ],
        ]);
    }
}
