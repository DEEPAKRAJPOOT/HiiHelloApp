<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Location;
use App\Http\Requests\Admin\LocationRequest;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.locations.index')->with(['custom_title' => 'Locations']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.locations.create',compact('languages'))->with(['custom_title' => 'Location', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationRequest $request)
    {
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('locations');
        $location = Location::create($data);

        if ($location->save()) {
            flash('Location created successfully!')->success();
        } else {
            flash('Unable to save location. Please try again later.')->error();
        }
        return redirect(route('admin.locations.index'));
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
    public function edit(Location $location)
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.locations.edit', compact('location','languages'))->with(['custom_title' => 'Location', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, Location $location)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($location) {
                $location->is_active = $request->value;
                if($location->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $location->update($data);
            if( $location->save() ) {
                flash('Location details updated successfully!')->success();
            } else {
                flash('Unable to location. Try again later')->error();
            }
            return redirect(route('admin.locations.index'));
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
            $locations = Location::select('id')->whereIn('custom_id',explode(',',$request->ids))->get();
            foreach($locations as $location){
                $location->locationTranslations()->delete();
                $location->delete();
            }
            // Location::whereIn('custom_id',explode(',',$request->ids))->delete();
            $content['status']=200;
            $content['message'] = "Location deleted successfully.";
            $content['count'] = Location::all()->count();
            return response()->json($content);
        }else{
            $location = Location::where('custom_id', $custom_id)->firstOrFail();
            $location->locationTranslations()->delete();
            $location->delete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Location deleted successfully.", 'count' => Location::all()->count());
                return response()->json($content);
            }else{
                flash('Location deleted successfully.')->success();
                return redirect()->route('admin.locations.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $locations = Location::with('locationTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $locations->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                    ->orWhereHas('locationTranslations', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $count = $locations->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $locations = $locations->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $locations = $locations->get();

        foreach ($locations as $location) {
            $params = [
                'checked'       =>  ($location->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $location->is_active,
                'class'         =>  '',
                'id'            =>  $location->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $location->id,
                'name'          =>  $location->locationTransDefault ? $location->locationTransDefault->name : "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Location', 'id' => $location->custom_id], $location)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $location->custom_id)->render(),
            ];
        }
        return $records;
    }
}
