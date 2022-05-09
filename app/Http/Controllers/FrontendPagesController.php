<?php

namespace App\Http\Controllers;

use App\Http\Requests\Frontend\InquiryRequest;
use App\Models\CmsPage;
use App\Models\Setting;
use Illuminate\Http\Request;

class FrontendPagesController extends Controller
{
    public function index(){
        $settings = Setting::select('value')->whereIn('constant',['footer_text','instagram','facebook','twitter'])->get();

        if($settings->isNotEmpty()){
            $footer_text = $settings[0]->value; $instagram = $settings[1]->value;
            $facebook = $settings[2]->value; $twitter = $settings[3]->value;

            $random_file = rand(0,1);
            if($random_file == 0){
                return view('frontend.pages.hindi',compact('footer_text','instagram','facebook','twitter'));
            }
            return view('frontend.pages.english',compact('footer_text','instagram','facebook','twitter'));
        }
        return abort(404);
    }

    public function terms(){
        $setting = Setting::select('value')->whereConstant('footer_text')->firstOrFail();
        $footer_text = $setting->value;
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('terms-and-conditions')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page','footer_text'))->withTitle($page->getTitle());
    }
    
    public function privacy(){
        $setting = Setting::select('value')->whereConstant('footer_text')->firstOrFail();
        $footer_text = $setting->value;
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('privacy')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page','footer_text'))->withTitle($page->getTitle());
    }

    public function about(){
        $setting = Setting::select('value')->whereConstant('footer_text')->firstOrFail();
        $footer_text = $setting->value;
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('about-us')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page','footer_text'))->withTitle($page->getTitle());
    }

    public function communityAndSafety(){
        $setting = Setting::select('value')->whereConstant('footer_text')->firstOrFail();
        $footer_text = $setting->value;
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('community-and-safety')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page','footer_text'))->withTitle($page->getTitle());
    }
    
}
