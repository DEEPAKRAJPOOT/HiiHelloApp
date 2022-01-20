<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /* ------------------------------------------ REGISTER ------------------------------------------  */

    public function test_generate_checksum_validation()
    {
        $data = [
            'Accept'        =>  'application/json',
        ];
        $this->postJson(route('api.generate-checksum'),$data)
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
            'Accept'        =>  'application/json',
            'x-language'    =>  config('utility.default_lang_code'),
            'contact_no'    =>  123456789,
        ];
        $this->postJson(route('api.generate-checksum'),$data)
        ->assertStatus(200)
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
}