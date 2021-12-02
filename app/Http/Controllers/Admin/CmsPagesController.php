<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
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
        return view('admin.pages.cms.edit', ['page' => $page])->with(['custom_title' => 'Page']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CmsPage $page) {
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            'description' => 'required',
        ]);
        $request['edited_by'] = Auth::id();
        if ($request->has('image')) {
            if ($page->file) {
                Storage::delete($page->file);
            }

            $path = $request->file('image')->store('general/files');
            $page->file = $path;
        }

        $page->fill($request->all());
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
        $users = CmsPage::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $users->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                ->orWhere('description','like', "%{$search}%");
               });
        }

        $count = $users->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $users = $users->get();
        foreach ($users as $user) {

            $params = [
                'checked' => ($user->is_active == 'y' ? 'checked' : ''),
                'getaction' => $user->display_upload,
                'class' => '',
                'id' => $user->id,
            ];

            $records['data'][] = [
                'id' => $user->id,
                'title' => $user->title,
                'description' => $user->description,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'User', 'id' => $user->id], $user)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $user->id)->render(),
            ];
        }
        // dd($records);
        return $records;
    }
}
