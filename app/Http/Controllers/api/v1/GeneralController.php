<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Storage};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use App\Http\Resources\v1\{LanguageResource, CmsResource, CountryResource, LocationResource, InterestResource, FaqResource, ProfileDetailResource, PersonalityResource, LocationTransResource, LocationSearchResource, DeviceTokenResource};
use App\Http\Requests\Api\General\{PaginationRequest, LocationRequest, ProfileDetailRequest, InterestRequest};
use App\Http\Requests\Api\User\{AddDeviceTokenRequest, GetDeviceTokenRequest};
use App\Models\{User, Language, CmsPage, Country, Location, Interest, Faq, DeviceToken, ProfileDetail, AppDetail, Personality, LocationTranslation};
use Illuminate\Support\Facades\Redis;
use App\Http\Traits\RedisTrait;

class GeneralController extends Controller
{
    use RedisTrait;
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get App Status
    public function appStatus()
    {
        $country        =   Country::select('updated_at')->orderBy('updated_at', 'DESC')->first();
        $cms_page       =   CmsPage::select('updated_at')->orderBy('updated_at', 'DESC')->first();
        $location       =   Location::select('updated_at')->orderBy('updated_at', 'DESC')->first();
        $attributes     =   ProfileDetail::whereIsActive('y')->distinct()->pluck('attribute')->toArray();
        $app_details    =   AppDetail::limit(4)->get();

        $verification_data = [];
        if ($app_details->isNotEmpty()) {
            $verification_data = [
                'male'   =>  [
                    'image_url' =>  generateURL($app_details[0]->value),
                    'video_url' =>  generateURL($app_details[1]->value),
                ],
                'female'   =>  [
                    'image_url' =>  generateURL($app_details[2]->value),
                    'video_url' =>  generateURL($app_details[3]->value),
                ],
            ];
        }

        $this->response['data'] = [
            'version'   =>  [
                'ios'       =>  [
                    'latest'    =>  '1.0',
                    'minimum'   =>  '1.0',
                ],
                'android'   =>  [
                    'latest'    =>  '1.0',
                    'minimum'   =>  '1.0',
                ],
            ],
            'common_age'    =>  [
                'min_age'   =>  config('utility.profile.common_age.min_age'),
                'max_age'   =>  config('utility.profile.common_age.max_age'),
            ],
            'updates'   =>  [
                'countries'     =>  $country ? $country->updated_at : "",
                'cms_page'      =>  $cms_page ? $cms_page->updated_at : "",
                'location'      =>  $location ? $location->updated_at : "",
            ],
            'profile_details'   =>  [
                'api_url'       =>  route('api.profile.get-details'),
                'attributes'    =>  $attributes,
            ],
            'links' =>  [
                'storage'   =>  config("utility.s3.prefix_url"),
                'terms'     =>  [
                    'en'    =>  route('terms', ['device' => 'mobile']),
                ],
                'privacy'   =>  [
                    'en'    =>  route('privacy.policy', ['device' => 'mobile']),
                ],
                'about'     =>  [
                    'en'    =>  route('about.us', ['device' => 'mobile']),
                ],
                'community_safety'     =>  [
                    'en'    =>  route('community.safety', ['device' => 'mobile']),
                ],
            ],
            'verification_details'  =>  $verification_data,
        ];
        $this->status = Response::HTTP_OK;
        $this->response['meta']['message'] = trans('api.list', ['entity' => __('App details')]);
        return $this->returnResponse();
    }

    // Get Countries List
    public function getCountries(Request $request)
    {
        try {
            $countries = Country::with('countryTranslation')->whereIsActive('y')->get();
            if ($countries->isNotEmpty()) {
                return (CountryResource::collection($countries))->additional([
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.list', ['entity' => __('Countries')]),
                    ]
                ]);
            } else {
                $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Countries')]);
                $this->status = Response::HTTP_NOT_FOUND;
            }
        } catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\Country':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Countries")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e, 'get_countries');
        }
        return $this->returnResponse();
    }

    // Get CMS Pages List (T&C, Privacy Policy, About Us)
    public function getCmsPages(Request $request)
    {
        try {
            $redisKey = config('redis.key.get-cms-pages');
            if ($this->cacheExist($redisKey)) {
                $cms_pages = $this->getCache($redisKey);
            } else {
                $cms_pages = CmsPage::with('cmsPageTranslation')->get();
                if ($this->cacheAllow()) {
                    $this->setCache($redisKey, $cms_pages);
                    $cms_pages = $this->getCache($redisKey);
                }
            }

            if (count($cms_pages) > 0) {
                return (CmsResource::collection($cms_pages))
                    ->additional([
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Cms Pages')]),
                        ]
                    ]);
            } else {
                $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Cms Pages')]);
                $this->status = Response::HTTP_NOT_FOUND;
            }
        } catch (ModelNotFoundException $exception) {
            switch ($exception->getModel()) {
                case 'App\Models\CmsPage':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Cms Pages")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e, 'get_cms_pages');
        }
        return $this->returnResponse();
    }

    // Get Locations List
    public function getLocations(Request $request)
    {
        $locationRequest = new LocationRequest();
        if ($this->apiValidator($request->all(), $locationRequest->rules())) {
            try {
                $search = $request->search;
                $lang = app()->getLocale();

                $locations = Location::select(
                    'locations.id',
                    'locations.custom_id',
                    'locations.is_active',
                    'location_translations.name as location_name'
                )
                    ->join('location_translations', 'locations.id', '=', 'location_translations.location_id')
                    ->where('location_translations.locale', $lang)
                    ->orderBy('location_translations.name');

                if (!empty($search)) {
                    $locations = $locations->whereHas('locationTranslation', function ($query) use ($search) {
                        $query->where('name', 'like', "{$search}%");
                    });
                }
                $count = $locations->count();
                $locations = $locations->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();
                if ($locations->isNotEmpty()) {
                    return (LocationSearchResource::collection($locations))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Locations')]),
                        ]
                    ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Locations')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Locations")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_locations');
            }
        }
        return $this->returnResponse();
    }

    // Get Locations List In All Languages
    public function getLocationsTrans(Request $request)
    {
        $locationRequest = new LocationRequest();
        if ($this->apiValidator($request->all(), $locationRequest->rules())) {
            try {
                $search = $request->search;
                $locations = Location::with('locationTranslations')->whereHas('locationTranslations');

                if (!empty($search)) {
                    $locations = $locations->whereHas('locationTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                }
                $count = $locations->count();
                $locations = $locations->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();
                if ($locations->isNotEmpty()) {
                    return (LocationTransResource::collection($locations))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Locations')]),
                        ]
                    ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Locations')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Locations")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_locations_trans');
            }
        }
        return $this->returnResponse();
    }

    // Get Interests List
    public function getInterests(Request $request)
    {
        $interestRequest = new InterestRequest();
        if ($this->apiValidator($request->all(), $interestRequest->rules())) {
            try {
                $search = $request->search;
                $interests = Interest::with([
                    'parentInterest:id,custom_id', 'masterInterest:id,custom_id',
                    'interestTranslation:id,interest_id,title'
                ]);
                // ->whereHas('location',function($query) use ($request) {
                //    $query->whereCustomId($request->location_id)->whereIsActive('y');
                // });

                if (!empty($search)) {
                    $interests = $interests->whereHas('interestTranslation', function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%");
                    });
                }

                if (!empty($request->parent_id)) {
                    $interests = $interests->whereNotNull('parent_id')
                        ->whereHas('parentInterest', function ($query) use ($request) {
                            $query->whereCustomId($request->parent_id)->whereIsActive('y');
                        });
                } else {
                    $interests = $interests->whereNull('parent_id');
                    if (!empty($request->level)) {
                        $interests->whereLevel($request->level);
                    }
                }

                $interests = $interests->whereIsActive('y')->withCount('subInterests');
                // ->withCount(['subInterests' => function ($query) use ($request) {
                //     $query->whereHas('location',function($q) use ($request) {
                //        $q->whereCustomId($request->location_id)->whereIsActive('y');
                //     });
                // }]);

                $count = $interests->count();
                $interests = $interests->orderBy('sequence')
                    ->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                if ($interests->isNotEmpty()) {
                    return (InterestResource::collection($interests))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Interests')]),
                        ]
                    ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Interests')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Interest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Interests")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_interests');
            }
        }
        return $this->returnResponse();
    }

    // Get Presonlaties 
    public function getPersonalities(Request $request)
    {
        $paginationRequest = new PaginationRequest();
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
            try {
                $personalities = Personality::with('personalityTranslation')->whereIsActive('y');
                $count = $personalities->count();

                $personalities = $personalities->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();
                if ($personalities->isNotEmpty()) {
                    return (PersonalityResource::collection($personalities))
                        ->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.list', ['entity' => __('Personalities')]),
                            ]
                        ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Personalities')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Personality':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Personalities")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_personalities');
            }
        }
        return $this->returnResponse();
    }

    // Get Faq Question And Answers
    public function getFaqs(Request $request)
    {
        $paginationRequest = new PaginationRequest();
        if ($this->apiValidator($request->all(), $paginationRequest->rules())) {
            try {
                $faqs = Faq::with('faqTranslation')->whereIsActive('y');
                $count = $faqs->count();

                $faqs = $faqs->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();
                if ($faqs->isNotEmpty()) {
                    return (FaqResource::collection($faqs))
                        ->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.list', ['entity' => __('Faqs')]),
                            ]
                        ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Faqs')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Faq':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Faqs")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_faqs');
            }
        }
        return $this->returnResponse();
    }

    // Get Details For Setup Profile
    public function getProfileDetails(Request $request)
    {
        $profileDetailRequest = new ProfileDetailRequest();
        if ($this->apiValidator($request->all(), $profileDetailRequest->rules())) {
            try {
                $search = $request->search;
                $profile_details = ProfileDetail::with('profileDetailTranslation')->whereIsActive('y');

                if (!empty($request->attribute)) {
                    $profile_details = $profile_details->whereAttribute($request->attribute);
                }
                if (!empty($search)) {
                    $profile_details = $profile_details->whereHas('profileDetailTranslation', function ($query) use ($search) {
                        $query->where('value', 'like', "%{$search}%");
                    });
                }
                $count = $profile_details->count();
                $profile_details = $profile_details->limit($request->limit ?? config('utility.pagination.limit'))
                    ->offset($request->offset ?? config('utility.pagination.offset'))
                    ->get();

                if ($profile_details->isNotEmpty()) {
                    return (ProfileDetailResource::collection($profile_details))
                        ->additional([
                            'meta' => [
                                'limit'     =>  $request->limit,
                                'offset'    =>  $request->offset,
                                'total'     =>  $count,
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.list', ['entity' => __('Profile details')]),
                            ]
                        ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Profile details')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\ProfileDetail':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Profile details")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_profile_details');
            }
        }
        return $this->returnResponse();
    }

    // Check Image Moderation Things
    public function checkImageModeration(Request $request)
    {
        $rules = [
            'image_path'     =>  'required|string',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $api_url    =   config('utility.image_moderation.api_url');
                $api_user   =   config('utility.image_moderation.api_user');
                $api_secret =   config('utility.image_moderation.api_secret');
                // $models     =   'nudity'; // We can also pass using comma values if we have multiple models
                $models     =   "nudity,text"; // We can also pass using comma values if we have multiple models
                $image_path =   $request->image_path;

                $client     =   new \GuzzleHttp\Client();
                $file       =   fopen($image_path, 'r');
                $response   =   $client->request(
                    'POST',
                    $api_url,
                    [
                        'query' => [
                            'api_user'      =>  $api_user,
                            'api_secret'    =>  $api_secret,
                            'models'        =>  $models
                        ],
                        'multipart' => [
                            [
                                'name'      =>  'media',
                                'contents'  =>  $file
                            ]
                        ]
                    ]
                );

                $output = json_decode($response->getBody());

                if ($output->status == 'success') {
                    return $output;
                } else {
                    $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __('Image Moderation')]);
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->status = Response::HTTP_NOT_FOUND;
                $this->storeErrorLog($e, 'image_moderation');
            }
        }
        return $this->returnResponse();
    }

    // Store Device Token
    public function storeDeviceToken(Request $request)
    {
        $addDeviceTokenRequest = new AddDeviceTokenRequest();
        if ($this->apiValidator($request->all(), $addDeviceTokenRequest->rules())) {
            try {
                $user = $request->user();
                DeviceToken::updateOrCreate([
                    'user_id'       =>  $user->id ?? NULL,
                ], [
                    'token'         =>  $request->token,
                    'type'          =>  $request->type,
                    'device_name'   =>  $request->device,
                    'os_name'       =>  $request->os,
                    'os_version'    =>  $request->version,
                    'app_version'   =>  $request->app_version,
                ]);
                $this->response['meta']['message'] = trans('api.add', ['entity' => __('Device token')]);
                $this->status = Response::HTTP_OK;
            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.went_wrong');
                $this->status = Response::HTTP_NOT_FOUND;
                $this->storeErrorLog($e, 'add_device_token');
            }
        }
        return $this->returnResponse();
    }

    // Get Device Token
    public function getDeviceToken(Request $request)
    {
        $getDeviceTokenRequest = new GetDeviceTokenRequest();
        if ($this->apiValidator($request->all(), $getDeviceTokenRequest->rules())) {
            try {
                $user_id = $request->user_id;
                $deviceToken = DeviceToken::whereHas('user', function ($query) use ($user_id) {
                    $query->whereCustomId($user_id)->whereIsActive('y');
                })->latest()->firstOrFail();

                $this->status = Response::HTTP_OK;
                return (new DeviceTokenResource($deviceToken))->additional([
                    'meta'  =>  [
                        'message'   =>  trans('api.list', ['entity' =>  __('Device token')]),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\DeviceToken':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Device token")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'get_device_token');
            }
        }
        return $this->returnResponse();
    }

    // Generate AWS S3 Bucket Upload URL
    public function generateAwsUrl(Request $request)
    {
        $rules = [
            'path'          =>  'required|in:chat,user_images,user_videos,user_voice',
            'extension'     =>  'required|string',
            'contentType'   =>  'required',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            $user = $request->user() ?? NULL;
            $time = \Carbon\Carbon::now()->timestamp;

            if ($request->path == 'chat') {
                $fileName = 'message-media/' . $user->custom_id . '/' . $time . '-' . $user->custom_id . '.' . $request->extension;
            } elseif ($request->path == 'user_images') {
                $fileName = 'users/images/' . $time . '-' . $user->custom_id . '.' . $request->extension;
            } elseif ($request->path == 'user_videos') {
                $fileName = 'users/videos/' . $time . '-' . $user->custom_id . '.' . $request->extension;
            } elseif ($request->path == 'user_voice') {
                $fileName = 'users/voice/' . $time . '-' . $user->custom_id . '.' . $request->extension;
            } else {
                $this->status = Response::HTTP_FORBIDDEN;
                $this->response['meta']['message'] = trans('api.went_wrong');
                return $this->returnResponse();
            }

            try {
                $s3Client = new \Aws\S3\S3Client([
                    'region' => config('filesystems.disks.s3.region'),
                    'version' => '2006-03-01',
                ]);

                $cmd = $s3Client->getCommand(
                    'PutObject',
                    [
                        'Bucket'        => config('filesystems.disks.s3.bucket'),
                        'ContentType'   => $request->contentType,
                        'Key'           => $fileName,
                        'Metadata'      => [
                            'user-id'   => $user->custom_id,
                        ],
                    ]
                );

                $url = $s3Client->createPresignedRequest($cmd, config('utility.s3.upload_expiry'));
                $this->response['data']['url'] = (string) $url->getUri();
                $this->response['data']['filename'] = (string) $fileName;
                $this->response['data']['timestamp'] = $time;
                $this->response['data']['exipry_time'] = config('utility.s3.upload_expiry');
                $this->response['meta']['message'] = trans('api.dynamic-link.success');
                $this->status = Response::HTTP_OK;
            } catch (Exception $exception) {
                $this->response['meta']['message'] = trans('api.went_wrong');
            }
        }
        return $this->returnResponse();
    }

    // Get All Languages List
    /*
    public function getLanguages()
    {
        try{
            $languages = Language::whereIsActive('y')->get();
            if($languages->isNotEmpty()){
                return (LanguageResource::collection($languages))
                        ->additional([
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Languages')]),
                        ] ]);
            }else{
                $this->status = Response::HTTP_FORBIDDEN;
                $this->response['meta']['message']  = trans('api.not_found',['entity' => __('Languages')]);
            }
        } catch(ModelNotFoundException $exception) {                
            switch ($exception->getModel()) {
                case 'App\Models\Language':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Languages")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e,'get_languages');
        }
        return $this->returnResponse();
    }
    */
}
