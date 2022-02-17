<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StateRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\Language;


class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.general.states.index')->with(['custom_title' => 'States']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->select('hint','language','lang_code')->get();
        $countries = Country::whereIsActive('y')->get();
        return view('admin.pages.general.states.create',compact('countries','languages'))->with(['custom_title' => 'State', 'default_lang' => config('utility.default_lang_code')]);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StateRequest $request)
    {
    
        $data = $this->getLangStoreData($request);
        $data['custom_id'] = getUniqueString('states');
        $data['country_id'] = $request->country_id;
      
        $state = State::create($data);
        if ($state->save()) {
            flash('state created successfully!')->success();
        } else {
            flash('Unable to save state. Please try again later.')->error();
        }
        return redirect(route('admin.states.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function edit(State $state)
    {
        $countries = Country::whereIsActive('y')->get();
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.general.states.edit', compact('state','countries','languages'))->with(['custom_title' => 'state', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StateRequest $request, State $state)
    {
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            if ($state) {
                $state->is_active = $request->value;
                if ($state->save()) {
                    $content['status'] = 200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $data['country_id'] = $request->country_id;

            $state->update($data);
            if ($state->save()) {
                flash('User details updated successfully!')->success();
            } else {
                flash('Unable to update user. Try again later')->error();
            }
            return redirect(route('admin.states.index'));
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
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            $states=State::whereIn('custom_id', explode(',', $request->ids))->delete();
            // foreach($states as $state){
            //     $state->stateTranslations()->delete();
            //     $state->delete();
            // }
            $content['status'] = 200;
            $content['message'] = "state deleted successfully.";
            $content['count'] = State::all()->count();
            return response()->json($content);
        } else {
            $state = State::where('custom_id', $id)->firstOrFail();
            $state->stateTranslations()->delete();
            $state->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "state deleted successfully.", 'count' => State::all()->count());
                return response()->json($content);
            } else {
                flash('state deleted successfully.')->success();
                return redirect()->route('admin.states.index');
            }
        }
    }



    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $states = State::with('country')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $states->where(function ($query) use ($search) {
                $query->where('custom_id', 'like', "%{$search}%")
                ->orWhereHas('stateTranslations', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }); 
            });
        }

        $count = $states->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];
        
        $states = $states->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $states = $states->get();
      
        foreach ($states as $state) {

            $params = [
                'checked' => ($state->is_active == 'y' ? 'checked' : ''),
                'getaction' => $state->is_active,
                'class' => '',
                'id' => $state->custom_id,
            ];

            $records['data'][] = [
                'id' => $state->id,
                'name' => $state->translate(config('utility.default_lang_code')) ? $state->translate(config('utility.default_lang_code'))->name : "",
                'country_name' => $state->country ? $state->country->translate(config('utility.default_lang_code')) ? $state->country->translate(config('utility.default_lang_code'))->name : "" : "",
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'States', 'id' => $state->custom_id], $state)->render(),
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $state->custom_id)->render(),
            ];

        }
        return $records;
    }

}
