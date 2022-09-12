<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Personality;
use App\Models\Language;
use App\Http\Requests\Admin\PersonalityRequest;

class PersonalityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.personalities.index')->with(['custom_title' => 'Personalities']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.personalities.create',compact('languages'))->with(['custom_title' => 'Personality', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PersonalityRequest $request)
    {
        $path = NULL;
        if( $request->has('image') ) {
            $path = $request->file('image')->store('personality');
        }
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('personalities');
        $personality = Personality::create($data);
        $personality->image = $path;

        if ($personality->save()) {
            flash('Personality Type created successfully!')->success();
        } else {
            flash('Unable to save personality type. Please try again later.')->error();
        }
        return redirect(route('admin.personalities.index'));

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
    public function edit(Personality $personality)
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.personalities.edit', compact('personality','languages'))->with(['custom_title' => 'Personality', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonalityRequest $request, Personality $personality)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($personality) {
                $personality->is_active = $request->value;
                if($personality->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $path = $personality->image;

            if( $request->hasFile('image') ) {
                if( $personality->image){ Storage::delete($personality->image); }
                $path = $request->image->store('personality');
            }
            $data = $this->getLangStoreData($request);
            $data['image'] = $path;
            $personality->update($data);

            if( $personality->save() ) {
                flash('Personality Type details updated successfully!')->success();
            } else {
                flash('Unable to personality type. Try again later')->error();
            }
            return redirect(route('admin.personalities.index'));
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
            $personalities = Personality::select('id')->whereIn('custom_id',explode(',',$request->ids))->get();
            foreach($personalities as $personality){
                if( $personality->image ){ Storage::delete($personality->image); }
                $personality->personalityTranslations()->delete();
                $personality->delete();
            }
            // Personality::whereIn('custom_id',explode(',',$request->ids))->delete();
            $content['status']=200;
            $content['message'] = "Personality deleted successfully.";
            $content['count'] = Personality::all()->count();
            return response()->json($content);
        }else{
            $personality = Personality::where('custom_id', $id)->firstOrFail();
            $personality->personalityTranslations()->delete();
            if( $personality->image ){ Storage::delete($personality->image); }
            $personality->delete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Personality deleted successfully.", 'count' => Personality::all()->count());
                return response()->json($content);
            }else{
                flash('Personality deleted successfully.')->success();
                return redirect()->route('admin.personalities.index');
            }
        }
    }

     public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $personalities = Personality::with('personalityTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $personalities->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhereHas('personalityTranslations', function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }

        $count = $personalities->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $personalities = $personalities->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $personalities = $personalities->get();

        foreach ($personalities as $personality) {
            $params = [
                'checked'       =>  ($personality->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $personality->is_active,
                'class'         =>  '',
                'id'            =>  $personality->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $personality->id,
                'title'         =>  $personality->personalityTransDefault ? $personality->personalityTransDefault->title : "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'personality', 'id' => $personality->custom_id], $personality)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $personality->custom_id)->render(),
            ];
        }
        return $records;
    }
}
