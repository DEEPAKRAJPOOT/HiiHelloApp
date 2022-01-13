<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Passion;
use App\Http\Requests\Admin\PassionRequest;

class PassionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.passions.index')->with(['custom_title' => 'Passion Management']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.passions.create',compact('languages'))->with(['custom_title' => 'Passion', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PassionRequest $request)
    {                    
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('passions');
        $passion = Passion::create($data);

        if ($passion->save()) {
            flash('Passion created successfully!')->success();
        } else {
            flash('Unable to save passion. Please try again later.')->error();
        }
        return redirect(route('admin.passions.index'));
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
    public function edit(Passion $passion)
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.passions.edit', compact('passion','languages'))->with(['custom_title' => 'Passions', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PassionRequest $passion)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($passion) {
                $passion->is_active = $request->value;
                if($passion->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $passion->update($data);
            if( $passion->save() ) {
                flash('Passion details updated successfully!')->success();
            } else {
                flash('Unable to passion. Try again later')->error();
            }
            return redirect(route('admin.passions.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $passions = Passion::select('id')->whereIn('custom_id',explode(',',$request->ids))->get();
            foreach($passions as $passion){
                $passion->passionTranslations()->delete();
                $passion->delete();
            }
            // Passion::whereIn('custom_id',explode(',',$request->ids))->delete();
            $content['status']=200;
            $content['message'] = "Passion deleted successfully.";
            $content['count'] = Passion::all()->count();
            return response()->json($content);
        }else{
            $passion = Passion::where('custom_id', $id)->firstOrFail();
            $passion->passionTranslations()->delete();
            $passion->delete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Passion deleted successfully.", 'count' => Passion::all()->count());
                return response()->json($content);
            }else{
                flash('Passion deleted successfully.')->success();
                return redirect()->route('admin.passions.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $passions = Passion::with('passionTranslations')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $passions->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhereHas('passionTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                             ->orWhere('type', 'like', "%{$search}%");
                    });
            });
        }

        $count = $passions->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $passions = $passions->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $passions = $passions->get();

        foreach ($passions as $passion) {
            $params = [
                'checked'       =>  ($passion->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $passion->is_active,
                'class'         =>  '',
                'id'            =>  $passion->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $passion->id,
                'name'          =>  $passion->translate(config('utility.default_lang_code')) ? $passion->translate(config('utility.default_lang_code'))->name : "",
                'type'          =>  $passion->translate(config('utility.default_lang_code')) ? $passion->translate(config('utility.default_lang_code'))->type : "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Passion', 'id' => $passion->custom_id], $passion)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $passion->custom_id)->render(),
            ];
        }
        return $records;
    }
}
