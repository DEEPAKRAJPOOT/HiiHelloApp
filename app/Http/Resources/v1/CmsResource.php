<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\RedisTrait;

class CmsResource extends JsonResource
{
    use RedisTrait;
    
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if( $this->cacheExist(config('redis.key.get-cms-pages')) ){
            return [ 
                'id'            =>  $this->custom_id ?? "",
                // 'title'         =>  $this->getTitle() ?? "",
                // 'description'   =>  $this->getDescription() ?? "",
                'title'         =>  $this->cms_page_translation ? $this->cms_page_translation->title : "",
                'description'   =>  $this->cms_page_translation ? $this->cms_page_translation->description : "",
                'hint'          =>  $this->hint ?? "",
                'image'         =>  generateURL($this->file) ?? "",
            ];
        }else{
            return [ 
                'id'            =>  $this->custom_id ?? "",
                // 'title'         =>  $this->getTitle() ?? "",
                // 'description'   =>  $this->getDescription() ?? "",
                'title'         =>  $this->cmsPageTranslation ? $this->cmsPageTranslation->title : "",
                'description'   =>  $this->cmsPageTranslation ? $this->cmsPageTranslation->description : "",
                'hint'          =>  $this->hint ?? "",
                'image'         =>  generateURL($this->file) ?? "",
            ];
        }
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'meta' => [ 
                'api'               =>  'v.1.0',
                'url'               =>  url()->current(),
                'language'          =>  app()->getLocale(),
            ],
        ];
    }
}
