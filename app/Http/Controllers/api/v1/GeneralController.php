<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\v1\LanguageCollection;
use App\Http\Resources\v1\CmsCollection;
use App\Models\Language;
use App\Models\CmsPage;

class GeneralController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function getLanguages()
    {
        $languages = Language::whereIsActive('y')->get();
        if($languages->isNotEmpty()){
            return (new LanguageCollection($languages))
                    ->additional([
                    'meta' => [
                        'message'   =>  trans('api.list',['entity' => 'Languages']),
                    ] ]);
        }else{
            $this->status = $this->statusArr['forbidden'];
            $this->response['meta']['message']  = trans('api.not_found',['entity' => 'Languages']);
        }
        $this->response['meta']['api'] = $this->getVersion();
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }

    // Get CMS Pages List(T&C, Privacy Policy)
    public function getCmsPages(Request $request)
    {
        $cms_pages = CmsPage::get();
        if(!$cms_pages->isEmpty()){
            return (new CmsCollection($cms_pages))
            ->additional([
                'meta' => [
                    'message' => trans('api.list',['entity' => 'Cms Pages']),
                ] ]);
        }else{
            $this->response['meta']['message']  =   trans('api.not_found',['entity' => 'Cms Pages']); 
            $this->status = $this->statusArr['not_found'];     
        }
        $this->response['meta']['api']      =   $this->getVersion();
        $this->response['meta']['url']      =   url()->current();
        return $this->return_response();
    }
}
