<?php

namespace App\Http\Controllers;

use App\Http\Requests\Frontend\InquiryRequest;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class FrontendPagesController extends Controller
{
    public function terms()
    {
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('terms-and-conditions')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page'))->withTitle($page->getTitle());
    }
    
    public function privacy()
    {
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('privacy')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page'))->withTitle($page->getTitle());
    }

    public function about()
    {
        $page = CmsPage::with('cmsPageTranslations')->whereSlug('about-us')->firstOrFail();
        return view('frontend.pages.cms-page', compact('page'))->withTitle($page->getTitle());
    }
}
