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

    // public function test_user_register_validation()
    // {
    //     $this->withoutExceptionHandling();
    //     $this->postJson(route('api.user.register'),['Accept' => 'application/json'])
    //         ->assertStatus(412)
    //         ->assertJsonStructure([
    //             'meta' => [
    //                 'api','url','message'
    //             ],
    //                 'data'
    //         ])->assertJson([
    //             'meta'  =>  [
    //                 'api'       =>  'v.1.0',
    //                 'url'       =>  route('api.user.register'),
    //                 'language'  =>  'en',
    //                 'message'   =>  trans('validation.required', ['attribute' => 'first name'])
    //             ],
    //             'data' => NULL
    //         ]);
    // }

    // public function test_user_register_successfully()
    // {
    //     User::where('email','testqa1947@gmail.com')->forceDelete();
    //     User::where('contact_no','123456789')->forceDelete();
    //     $data = [
    //         'Accept'                        =>  'application/json',
    //         'first_name'                    =>  'Test First Name',
    //         'last_name'                     =>  'Test Last Name',
    //         'email'                         =>  'testqa1947@gmail.com',
    //         'contact_no'                    =>  '123456789',
    //         'password'                      =>  'Test@1947',
    //         'confirm_password'              =>  'Test@1947',
    //     ];
    //     $this->postJson(route('api.user.register'),$data)
    //         ->assertStatus(201)
    //         ->assertJsonStructure([
    //             'meta'  =>  [
    //                 'message','auth_token'
    //             ],
    //             'data'
    //         ]);
    // }
}