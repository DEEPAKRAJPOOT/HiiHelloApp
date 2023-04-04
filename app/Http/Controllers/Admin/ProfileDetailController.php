<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\ProfileDetail;
use App\Models\Language;
use App\Http\Requests\Admin\ProfileDetailRequest;
use File;
use Exception;

class ProfileDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.profile-details.index')->with(['custom_title' => 'Profile Details']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.profile-details.create',compact('languages'))->with(['custom_title' => 'Profile Details', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfileDetailRequest $request)
    {                    
        $data = $this->getLangStoreData($request);

        $slug = str_slug($request->attribute);
        $count = ProfileDetail::where('attribute', "like", "%{$slug}%")->count();
        $data['slug'] = $slug;
        if( $count != 0 ) {
            $data['slug'] = $slug . '-' . $count;
        }
        $data['attribute'] = $request->attribute;
        $data['type'] = 'string';
        $profile_detail = ProfileDetail::create($data);

        if ($profile_detail->save()) {
            flash('Profile Details created successfully!')->success();
        } else {
            flash('Unable to save profile details. Please try again later.')->error();
        }
        return redirect(route('admin.profile-details.index'));
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
    public function edit(ProfileDetail $profile_detail)
    {
        $languages = Language::whereIsActive('y')->get();
        return view('admin.pages.profile-details.edit', compact('profile_detail','languages'))->with(['custom_title' => 'Profile Detail', 'default_lang' => config('utility.default_lang_code')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileDetailRequest $request, ProfileDetail $profile_detail)
    {
        if(!empty($request->action) && $request->action == 'change_status') {
            $content = ['status'=>204, 'message'=>"something went wrong"];
            if($profile_detail) {
                $profile_detail->is_active = $request->value;
                if($profile_detail->save()) {
                    $content['status']=200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $data = $this->getLangStoreData($request);
            $data['attribute'] = $request->attribute;
            $data['type'] = 'string';

            $profile_detail->update($data);

            if (array_key_exists('attribute', $profile_detail->getDirty())) {
                $slug = str_slug($request->attribute);
                $count = ProfileDetail::where('id', '<>', $profile_detail->id)->where('slug', "like", "%{$slug}%")->count();
                $slug = $slug;
                if( $count != 0 ) {
                    $request['slug'] = $profile_detail->slug . '-' . $count;
                }
                $profile_detail->slug = $slug;
            }

            if( $profile_detail->save() ) {
                flash('Profile Details details updated successfully!')->success();
            } else {
                flash('Unable to profile detail. Try again later')->error();
            }
            return redirect(route('admin.profile-details.index'));
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
            $profile_details = ProfileDetail::select('id')->whereIn('slug',explode(',',$request->ids))->get();
            foreach($profile_details as $profile_detail){
                $profile_detail->profileDetailTranslations()->delete();
                $profile_detail->delete();
            }
            // ProfileDetail::whereIn('slug',explode(',',$request->ids))->delete();
            $content['status']=200;
            $content['message'] = "Profile Details deleted successfully.";
            $content['count'] = ProfileDetail::all()->count();
            return response()->json($content);
        }else{
            $profile_detail = ProfileDetail::where('slug', $id)->firstOrFail();
            $profile_detail->profileDetailTranslations()->delete();
            $profile_detail->delete();
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Profile Detail deleted successfully.", 'count' => ProfileDetail::all()->count());
                return response()->json($content);
            }else{
                flash('Profile Detail deleted successfully.')->success();
                return redirect()->route('admin.profile-details.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $profile_details = ProfileDetail::with('profileDetailTransDefault')->orderBy($sort_column, $sort_order);

        if ($search != '') {
            $profile_details->where(function ($query) use ($search) {
                $query->where('slug', 'like', "%{$search}%")
                    ->orWhere('attribute', 'like', "%{$search}%")
                    ->orWhereHas('profileDetailTranslations', function ($query) use ($search) {
                        $query->where('value', 'like', "%{$search}%");
                    });
            });
        }

        $count = $profile_details->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $profile_details = $profile_details->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);
        $profile_details = $profile_details->get();

        foreach ($profile_details as $profile_detail) {
            $params = [
                'checked'       =>  ($profile_detail->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $profile_detail->is_active,
                'class'         =>  '',
                'id'            =>  $profile_detail->slug,
            ];

            $records['data'][] = [
                'id'            =>  $profile_detail->id,
                'value'         =>  $profile_detail->profileDetailTransDefault ? $profile_detail->profileDetailTransDefault->value : "",
                'attribute'     =>  $profile_detail->attribute ?? "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Location', 'id' => $profile_detail->slug], $profile_detail)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $profile_detail->slug)->render(),
            ];
        }
        return $records;
    }

    public function csvUpload(Request $request)
    {
        @set_time_limit(0);
        $file = NULL;
        $status = false;
        $counter = 0;
        $message = "No new profile details found";
        if( $request->has('csvFile') ) {
            $path = $request->file('csvFile');
            // Open File
                $handle = fopen($path,'r');
                if( $handle !== false ) {
                    $readLine = fgetcsv($handle,1000,',');
                    while ( ($readLine = fgetcsv($handle,1000,',')) !== false ) {
                            
                        if( !empty($readLine[0]) && !empty($readLine[1]) && !empty($readLine[2])) {
                                
                            $slug = str_slug($readLine[1]);
                            $data = [
                                'slug'          =>  $slug,
                                'attribute'     =>  $readLine[0],
                                'type'          =>  'string',
                                'en'    =>  [
                                    'value'     =>  $readLine[1].' - '.$readLine[2],
                                ],
                            ];

                            $profile_detail = ProfileDetail::where('slug', "like", "%{$slug}%")->first();
                            if($profile_detail){
                                $profile_detail->update($data);
                            }else{
                                $profile_detail = ProfileDetail::create($data);
                            }
                            $profile_detail->save();

                            if( $profile_detail->wasRecentlyCreated )
                            $counter++;
                        }
                    }
                    $status = true;
                } else {
                    $message = "Unable to read file, please upload proper file.";
                }
        }
        if( $status = true && $counter >= 1) {
            $title = 'profile detail';
            if( $counter >= 2 )
                $title = 'profile details';
            flash($title.' added successfully!')->success();
        } else {
            flash($message)->important();
        }
        return redirect(route('admin.profile-details.index')); 
    }

    //SAMPLE CSV DOWNLOAD
    public function sampleCsvDownload(Request $request)
    {
        $data = [
            [
                'Attribute'         =>  'university_college',
                'Institute Name'    =>  'JAWAHARLAL DARDA INSTITUTE OF ENGINEERING & TECHNOLOGY',
                'State'             =>  'Maharashtra',
            ],
            [
                'Attribute'         =>  'university_college',
                'Institute Name'    =>  'PRIYADARSHINI BHAGWATI  COLLEGE OF ENGINEERING',
                'State'             =>  'Maharashtra',
            ],
            [
                'Attribute'         =>  'university_college',
                'Institute Name'    =>  'Y. B. PATIL POLYTECHNIC',
                'State'             =>  'Maharashtra',
            ],
        ];
           
        if (!File::exists(public_path()."/files")) {
            File::makeDirectory(public_path() . "/files");
        }

        $filename = public_path('files/'."sample_profile_details.csv");
        $handle   = fopen($filename, 'w+');
        try{
            chmod($filename,0777);
        }catch(Exception $e){}
        fputcsv($handle, array('Attribute', 'Institute Name', 'State'));

        foreach($data as $row) {
            fputcsv($handle, array(
                $row['Attribute'], $row['Institute Name'], $row['State']));
        }
        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, 'sample_profile_details.csv', $headers);
    }
}
