<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CmsPageRequest;
use App\Models\CmsPage;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CmsPagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $count = CmsPage::all()->count();
        return view('admin.pages.cms.index', compact('count'))->with(['custom_title' => __('CMS Pages')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        CmsPage::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CmsPage $page) {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.cms.edit', ['page' => $page, 'languages' => $languages])->with(['custom_title' => 'Page','default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CmsPageRequest $request, CmsPage $page) {
        $data = $this->getLangStoreData($request);

        $data['edited_by'] = Auth::id();
        if ($request->has('image')) {
            if ($page->file) {
                Storage::delete($page->file);
            }
            $path = $request->file('image')->store('general/files');
            $page->file = $path;
        }

        $page->update($data);
        if ($page->save()) {
            flash(trans('flash_message.update', ['entity' => 'Page details']))->success();
        } else {
            flash(trans('try_again'))->error();
        }

        return redirect(route('admin.pages.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        CmsPage::find($id);
    }

    /* Listing Details */
    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $cms_pages = CmsPage::with('cmsPageTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $cms_pages->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhereHas('cmsPageTranslations', function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
                });
            });
        }

        $count = $cms_pages->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $cms_pages = $cms_pages->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $cms_pages = $cms_pages->get();

        foreach ($cms_pages as $cms_page) {

            $params = [
                'checked' => ($cms_page->is_active == 'y' ? 'checked' : ''),
                'getaction' => $cms_page->display_upload,
                'class' => '',
                'id' => $cms_page->custom_id,
            ];

            $records['data'][] = [
                'id' => $cms_page->id,
                'title' => $cms_page->cmsPageTransDefault ? $cms_page->cmsPageTransDefault->title : "",
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $cms_page->custom_id], $cms_page)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $cms_page->custom_id)->render(),
            ];
        }
        return $records;
    }
}
