<?php

namespace App\Http\Controllers;

use App\Http\Requests\Frontend\InquiryRequest;
use App\Models\CmsPage;
use App\Models\Setting;
use Illuminate\Http\Request;

class FrontendPagesController extends Controller
{
    public function index(){
        $setting = Setting::select('value')->whereConstant('footer_text')->firstOrFail();
        $footer_text = $setting->value;

        $random_file = rand(0,1);
        if($random_file == 0){
            return view('frontend.pages.hindi',compact('footer_text'));
        }
        return view('frontend.pages.english',compact('footer_text'));
    }

    public function terms(){
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('terms-and-conditions')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page'))->withTitle($page->getTitle());
    }
    
    public function privacy(){
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('privacy')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page'))->withTitle($page->getTitle());
    }

    public function about(){
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('about-us')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page'))->withTitle($page->getTitle());
    }
}
