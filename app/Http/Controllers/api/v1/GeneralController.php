<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\v1\LanguageResource;
use App\Http\Resources\v1\CmsResource;
use App\Http\Resources\v1\CountryResource;
use App\Http\Resources\v1\LocationResource;
use App\Http\Resources\v1\InterestResource;
use App\Http\Resources\v1\FaqResource;
use App\Http\Requests\Api\General\PaginationRequest;
use App\Http\Requests\Api\General\LocationRequest;
use App\Http\Requests\Api\General\CountryRequest;
use App\Models\Language;
use App\Models\CmsPage;
use App\Models\Country;
use App\Models\Location;
use App\Models\Interest;
use App\Models\Faq;

class GeneralController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get App Status
    public function appStatus()
    {
        $country    =   Country::orderBy('updated_at', 'DESC')->first();
        $cms_page   =   CmsPage::orderBy('updated_at', 'DESC')->first();
        $location   =   Location::orderBy('updated_at', 'DESC')->first();

        $this->response['data'] = [
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
            'updates'   =>  [
                'countries'     =>  $country ? $country->updated_at : "",
                'cms_page'      =>  $cms_page ? $cms_page->updated_at : "",
                'location'      =>  $location ? $location->updated_at : "",
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
        ];
        $this->status = Response::HTTP_OK;
        $this->response['meta']['message'] = trans('api.list', ['entity' => __('App details')]);
        return $this->returnResponse();
    }

    // Get All Languages List
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
            return $this->returnResponse();
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
    }

    // Get Countries List
    public function getCountries(Request $request)
    {
        $rules = CountryRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $countries = Country::whereIsActive('y');
                if(!empty($request->search)){
                    $search = $request->search;
                    $countries = $countries->whereHas('countryTranslations', function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%");
                                });
                }
                $count = $countries->count();
                $countries = $countries->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();
                if($countries->isNotEmpty()){
                    return (CountryResource::collection($countries))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Countries')]),
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Countries')]); 
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Country':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Countries")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_countries');
            }
        }
        return $this->returnResponse();
    }

    // Get CMS Pages List (T&C, Privacy Policy, About Us)
    public function getCmsPages(Request $request)
    {
        try{
            $cms_pages = CmsPage::get();
            if($cms_pages->isNotEmpty()){
                return (CmsResource::collection($cms_pages))
                ->additional([
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.list', ['entity' => __('Cms Pages')]),
                    ] ]);
            }else{
                $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Cms Pages')]); 
                $this->status = Response::HTTP_NOT_FOUND;     
            }
            return $this->returnResponse();
        } catch(ModelNotFoundException $exception) {                
            switch ($exception->getModel()) {
                case 'App\Models\CmsPage':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Cms Pages")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        } catch (\Exception $e) {
            $this->storeErrorLog($e,'get_cms_pages');
        }
        return $this->returnResponse();
    }
    
    // Get Locations List
    public function getLocations(Request $request)
    {
        $rules = LocationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $locations = Location::whereIsActive('y');
                if(!empty($request->search)){
                    $search = $request->search;
                    $locations = $locations->whereHas('locationTranslations', function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%");
                                });
                }
                $count = $locations->count();
                $locations = $locations->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();
                if($locations->isNotEmpty()){
                    return (LocationResource::collection($locations))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Locations')]),
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Locations')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Location':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Locations")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_locations');
            }
        }
        return $this->returnResponse();
    }

    // Get Interests List
    public function getInterests(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $interests = Interest::whereIsActive('y');
                $count = $interests->count();
                $interests = $interests->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();
                if($interests->isNotEmpty()){
                    return (InterestResource::collection($interests))->additional([
                        'meta' => [
                            'limit'     =>  $request->limit,
                            'offset'    =>  $request->offset,
                            'total'     =>  $count,
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'message'   =>  trans('api.list', ['entity' => __('Interests')]),
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Interests')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\Interest':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Interests")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_interests');
            }
        }
        return $this->returnResponse();
    }

    // Get Faq Question And Answers
    public function getFaqs(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $faqs = Faq::whereIsActive('y');
                $count = $faqs->count();
                $faqs = $faqs->limit($request->limit ?? config('utility.pagination.limit'))
                            ->offset($request->offset ?? config('utility.pagination.offset'))
                            ->get();
                if($faqs->isNotEmpty()){
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
                        ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.not_found',['entity' => __('Faqs')]); 
                    $this->status = Response::HTTP_NOT_FOUND;     
                }
                return $this->returnResponse();
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\CmsPage':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Faqs")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'get_faqs');
            }
        }
        return $this->returnResponse();
    }
}
