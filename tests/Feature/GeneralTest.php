<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\InitialiseUserTrait;
use Tests\TestCase;

class GeneralTest extends TestCase
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

    public function test_app_status_successfully()
    {
        $this->postJson(route('api.app-status'),[])
        ->assertOk()
        ->assertJsonStructure([
            'data'  =>  [
                'version'   =>  [
                    'ios'       =>  [
                        'latest',
                        'minimum',
                    ],
                    'android'   =>  [
                        'latest',
                        'minimum',
                    ],
                ],
                'updates', 'links'
            ],
            'meta'  =>  [
                'url', 'api', 'message', 'language'
            ],
        ])
        ->assertJson([
            'data'  =>  [
                'version'   =>  [
                    'ios'       =>  [
                        'latest'    =>  '1.0',
                        'minimum'    =>  '1.0',
                    ],
                    'android'   =>  [
                        'latest'    =>  '1.0',
                        'minimum'    =>  '1.0',
                    ],
                ],
                'links' =>  [
                    'storage'   =>  config("utility.s3.prefix_url"),
                    'terms'     =>  [
                        'en'    =>  route('terms'),
                    ],
                    'privacy'   =>  [
                        'en'    =>  route('privacy.policy'),
                    ],
                    'about'     =>  [
                        'en'    =>  route('about.us'),
                    ],
                ],
            ],
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('App details')]),
            ]
        ]);
    }

    public function test_get_countries_successfully()
    {
        $this->postJson(route('api.get-countries'),[])
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Countries')]),
            ],
        ]);
    }

    public function test_get_cms_pages_successfully()
    {
        $this->postJson(route('api.get-cms-pages'),[])
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Cms Pages')]),
            ],
        ]);
    }

    public function test_get_locations_validation()
    {
        $data = [
            'search'    =>  'This is long text validation example test for get location api search filed. we need to chis if we pass more than one thousand fifty characters of more than how this test case work!!!',
        ];
        $this->postJson(route('api.get-locations'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
            'data'
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.max.string', ['attribute' => __('search'), 'max' => 150])
            ],
            'data' => NULL
        ]);
    }

    public function test_get_locations_successfully()
    {
        $this->postJson(route('api.get-locations'),[])
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Locations')]),
            ],
        ]);
    }

    public function test_get_interests_validation()
    {
        $data = [
            'limit'      =>  'Abc',
            'offset'     =>  0,
        ];
        $this->postJson(route('api.get-interests'),$data)
        ->assertStatus(412)
        ->assertJsonStructure([
            'meta' => [
                'api','url','message'
            ],
            'data'
        ])->assertJson([
            'meta'  =>  [
                'api'       =>  $this->getVersion(),
                'url'       =>  url()->current(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('validation.numeric', ['attribute' => __('limit') ])
            ],
            'data' => NULL
        ]);
    }

    public function test_get_interests_successfully()
    {
        $location = $this->getLocation();
        $data = [
            'location_id'   =>  $location->custom_id,
            'level'         =>  2,
        ];
        $this->postJson(route('api.get-interests'),$data)
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [
                'api','url','message', 'total'
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Interests')]),
            ],
        ]);
    }

    public function test_get_faqs_successfully()
    {
        $faq = $this->setFaq();
        $this->postJson(route('api.get-faqs'),[])
        ->assertOk()
        ->assertJsonStructure([
            'meta' => [
                'api','url','message', 'total'
            ],
        ])->assertJson([
            'meta'  =>  [
                'url'       =>  url()->current(),
                'api'       =>  $this->getVersion(),
                'language'  =>  config('utility.default_lang_code'),
                'message'   =>  trans('api.list', ['entity' => __('Faqs')]),
            ],
        ]);
    }
}
