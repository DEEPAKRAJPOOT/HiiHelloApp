<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\v1\LanguageResource;
use App\Http\Resources\v1\CmsResource;
use App\Http\Resources\v1\CountryResource;
use App\Http\Requests\Api\General\PaginationRequest;
use App\Models\Language;
use App\Models\CmsPage;
use App\Models\Country;

class GeneralController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Get App Status
    public function appStatus()
    {
        $country = Country::orderBy('updated_at', 'DESC')->first();
        $cms_page = CmsPage::orderBy('updated_at', 'DESC')->first();

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
        $this->status = $this->statusArr['success'];
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
                $this->status = $this->statusArr['forbidden'];
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
        }
    }

    // Get Countries List
    public function getCountries(Request $request)
    {
        $rules = PaginationRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $countries = Country::whereIsActive('y');
                $count = $countries->count();
                $countries = $countries->limit($request->limit ?? config('utility.pagination.limit'))
                        ->offset($request->offset ?? config('utility.pagination.offset'))
                        ->get();
                if(!$countries->isEmpty()){
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
                    $this->status = $this->statusArr['not_found'];     
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
            }
        }
        return $this->returnResponse();
    }

    // Get CMS Pages List (T&C, Privacy Policy, About Us)
    public function getCmsPages(Request $request)
    {
        try{
            $cms_pages = CmsPage::get();
            if(!$cms_pages->isEmpty()){
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
                $this->status = $this->statusArr['not_found'];     
            }
            return $this->returnResponse();
        } catch(ModelNotFoundException $exception) {                
            switch ($exception->getModel()) {
                case 'App\Models\CmsResource':
                    $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Cms Pages")]);
                    break;
                default:
                    $this->response['meta']['message'] = trans('api.went_wrong');
                    break;
            };
        }
    }
}
