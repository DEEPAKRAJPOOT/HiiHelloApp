<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Interest;
use App\Models\Location;
use App\Http\Requests\Admin\InterestRequest;

class InterestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.interests.index')->with(['custom_title' => 'Interests']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        $parent_interests = Interest::with('interestTranslation')->whereIsActive('y')->get();
        $locations = Location::with('locationTranslation')->whereIsActive('y')->get();

        return view('admin.pages.interests.create',compact('languages','parent_interests','locations'))->with(['custom_title' => 'Interest', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InterestRequest $request)
    {
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('interests');
        $data['parent_id'] = $request->parent_id;
        $data['location_id'] = $request->location_id;

        $interest = Interest::create($data);
        if ($interest->save()) {
            flash('Interest created successfully!')->success();
        } else {
            flash('Unable to save interest. Please try again later.')->error();
        }
        return redirect(route('admin.interests.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Interest $interest)
    {
        $languages = Language::whereIsActive('y')->get();
        $parent_interests = Interest::with('interestTranslation')->where('id','!=',$interest->id)->whereIsActive('y')->get();
        $locations = Location::with('locationTranslation')->whereIsActive('y')->get();

        return view('admin.pages.interests.edit', compact('interest','languages','parent_interests','locations'))->with(['custom_title' => 'Interest', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InterestRequest $request, Interest $interest)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($interest) {
                $interest->is_active = $request->value;
                if($interest->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $data['parent_id'] = $request->parent_id;
            $data['location_id'] = $request->location_id;

            $interest->update($data);
            if( $interest->save() ) {
                flash('Interest details updated successfully!')->success();
            } else {
                flash('Unable to interest. Try again later')->error();
            }
            return redirect(route('admin.interests.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $custom_id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $interests = Interest::select('id')->whereIn('custom_id',explode(',',$request->ids))->get();
            foreach($interests as $interest){
                $interest->interestTranslations()->delete();
                $interest->delete();
            }
            // Interest::whereIn('custom_id',explode(',',$request->ids))->delete();
            $content['status']=200;
            $content['message'] = "Interest deleted successfully.";
            $content['count'] = Interest::all()->count();
            return response()->json($content);
        }else{
            $interest = Interest::where('custom_id', $custom_id)->firstOrFail();
            $interest->interestTranslations()->delete();
            $interest->delete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Interest deleted successfully.", 'count' => Interest::all()->count());
                return response()->json($content);
            }else{
                flash('Interest deleted successfully.')->success();
                return redirect()->route('admin.interests.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $interests = Interest::with('interestTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $interests->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhereHas('interestTranslations', function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $count = $interests->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $interests = $interests->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $interests = $interests->get();

        foreach ($interests as $interest) {
            $params = [
                'checked'       =>  ($interest->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $interest->is_active,
                'class'         =>  '',
                'id'            =>  $interest->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $interest->id,
                'title'         =>  $interest->interestTransDefault ? $interest->interestTransDefault->title : "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Interest', 'id' => $interest->custom_id], $interest)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $interest->custom_id)->render(),
            ];
        }
        return $records;
    }
}
