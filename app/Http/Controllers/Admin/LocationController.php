<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Location;
use App\Models\LocationTranslation;
use App\Models\User;
use App\Models\Interest;
use App\Http\Requests\Admin\LocationRequest;
use App\Http\Requests\Admin\MergeLocationRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use DB;
use Exception;
use Illuminate\Support\Facades\Crypt;

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
        // if(!empty($request->action) && $request->action == 'delete_all'){
        //     $content = ['status'=>204, 'message'=>"something went wrong"];
        //     $locations = Location::select('id')->whereIn('custom_id',explode(',',$request->ids))->get();
        //     foreach($locations as $location){
        //         $location->locationTranslations()->delete();
        //         $location->delete();
        //     }
        //     // Location::whereIn('custom_id',explode(',',$request->ids))->delete();
        //     $content['status']=200;
        //     $content['message'] = "Location deleted successfully.";
        //     $content['count'] = Location::all()->count();
        //     return response()->json($content);
        // }else{
        //     $location = Location::where('custom_id', $custom_id)->firstOrFail();
        //     $location->locationTranslations()->delete();
        //     $location->delete();
        //     if(request()->ajax()){
        //         $content = array('status'=>200, 'message'=>"Location deleted successfully.", 'count' => Location::all()->count());
        //         return response()->json($content);
        //     }else{
        //         flash('Location deleted successfully.')->success();
        //         return redirect()->route('admin.locations.index');
        //     }
        // }

        return redirect()->route('admin.locations.index');
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];

        $locations = Location::select("locations.id as id","locations.custom_id as custom_id","locations.is_active as is_active","location_translations.location_id as location_id","location_translations.name as name","location_translations.state as state");
        $locations = $locations->join("location_translations","location_translations.location_id","=","locations.id");
        $locations = $locations->where('location_translations.locale','en');
        if ($search != '') {
            $locations->where("location_translations.name","like",'%'.$search.'%');
            $locations->orWhere("location_translations.state","like",'%'.$search.'%');
        }
        $locations = $locations->where("locations.is_active","y");
        $count = $locations->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $locations = $locations->offset($offset)->limit($limit)->orderBy("location_translations.name", "asc");
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
                'name'          =>  $location->name ?? "",
                'state'         =>  $location->state ?? "",
                'active'        =>  view('admin.layouts.includes.switch', compact('params'))->render(),
                'action'        =>  view('admin.layouts.includes.actions')->with(['custom_title' => 'Location', 'id' => $location->custom_id], $location)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $location->custom_id)->render(),
            ];
        }
        return $records;
    }

    public function csvDownload(Request $request)
    {
        $down_file_name = 'Location Report';
        $location_reports = Location::select("locations.id as id","location_translations.name as name","location_translations.state as state",DB::raw("count(users.id) as total_users"))
                            ->join("users","users.location_id","=","locations.id")
                            ->join("location_translations","locations.id","=","location_translations.location_id")
                            ->where("users.new_location_id","=",'y')
                            ->where("locations.is_active","=",'y')
                            ->where("location_translations.locale","=",'en')
                            ->groupBy('location_translations.location_id')
                            ->orderBy('name','ASC')
                            ->get();

        $all_users          = User::count();
        $data = [];
        // echo "<pre>"; print_r($location_reports->toArray()); die();
        if (!$location_reports->isEmpty()) {
            foreach ($location_reports as $val) {
                $total_users = $val->total_users;
                $pr = $total_users/$all_users * 100;
                $data[] = [
                    'City Id'             =>  $val->id ? $val->id : "",
                    'City name'           =>  $val->name ? $val->name : "",
                    'State name'          =>  $val->state ? $val->state : "",
                    'Total Users'         =>  $total_users,
                    'Percentage'          =>  number_format($pr,2),
                ];
            }

            // echo "<pre>"; print_r($data); die();
            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            try{
                chmod($filename,0777);
            }catch(Exception $e){}
            fputcsv($handle, array(
                'City Id','City name','State name', 'Total Users', 'Percentage'  
            ));
            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['City Id'], $row['City name'],$row['State name'], $row['Total Users'], $row['Percentage']
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate transaction csv file. Try again later')->error();
        }
        return redirect(route('admin.locations.index'));
    }

    public function userlocationcsvDownload(Request $request)
    {
        $page = $request->page ? $request->page : 10;
        $skip = $request->skip ? $request->skip : 0;
        $down_file_name = 'User Location Report';
        $location_reports = User::select("users.id as id","users.account_id as account_id","users.location_id as location_id","location_translations.name as city_name","location_translations.state as state_name")
                            ->leftJoin("location_translations","location_translations.location_id","=","users.location_id")
                            ->where("location_translations.locale","=",'en')
                            ->groupBy('users.id')
                            ->limit($page)
                            // ->paginate($page);
                            ->skip($skip)
                            // ->offset(20)
                            ->get();

        $data = [];
        if ($request->is_print == 1) {
            echo "<pre>"; print_r($location_reports->toArray()); die();
        }
        if (!$location_reports->isEmpty()) {
            foreach ($location_reports as $val) {
                $data[] = [
                    'User Id'             =>  $val->id ? $val->id : "",
                    'Account Id'          =>  $val->account_id ? $val->account_id : "",
                    'User name'           =>  $val->full_name ? $val->full_name : "",
                    'Location Id'         =>  $val->location_id ? $val->location_id : "",
                    'City name'           =>  $val->city_name ? $val->city_name : "",
                    'State name'          =>  $val->state_name ? $val->state_name : "",
                ];
            }

            // echo "<pre>"; print_r($data); die();
            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            try{
                chmod($filename,0777);
            }catch(Exception $e){}
            fputcsv($handle, array(
                'User Id', 'Account Id', 'User name', 'Location Id', 'City name', 'State name'  
            ));
            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['User Id'], $row['Account Id'], $row['User name'], $row['Location Id'], $row['City name'], $row['State name']
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate transaction csv file. Try again later')->error();
        }
        return redirect(route('admin.locations.index'));
    } 

    public function usernotlocationcsvDownload(Request $request)
    {
        $new_location_id = $request->new_location_id ? $request->new_location_id : 'n';
        $limit = $request->limit ? $request->limit : 100;
        $skip = $request->skip ? $request->skip : 0;
        $from = $request->from;
        $to = $request->to;
        $down_file_name = 'User Not Location Translate Report';
        $location_reports = User::select('users.id as id','users.account_id as account_id','users.latitude as latitude','users.longitude as longitude','users.location_id as location_id','users.new_location_id as new_location_id','users.created_at as created_at','location_translations.name as city_name','location_translations.state as state_name')
                    ->leftJoin("location_translations","location_translations.location_id","=","users.location_id")
                    ->where("location_translations.locale","=",'en')
                    ->where("users.new_location_id",$new_location_id)
                    ->whereNotNull("users.latitude")
                    ->whereNotNull("users.longitude");
                    if (!empty($from) && !empty($to)) {
                        $location_reports = $location_reports->where('users.created_at','>=',$from);
                        $location_reports = $location_reports->where("users.created_at",'<=',$to);
                    }
                    $location_reports = $location_reports->groupBy('users.id');
                    $location_reports = $location_reports->limit($limit);
                    $location_reports = $location_reports->skip($skip);
                    $location_reports = $location_reports->get();

        $data = [];
        if ($request->is_print == 1) {
            echo "<pre>"; print_r($location_reports->toArray()); die();
        }
        if (!$location_reports->isEmpty()) {
            foreach ($location_reports as $val) {
                $data[] = [
                    'User Id'             =>  $val->id ? $val->id : "",
                    'Account Id'          =>  $val->account_id ? $val->account_id : "",
                    'User name'           =>  $val->full_name ? $val->full_name : "",
                    'Location Id'         =>  $val->location_id ? $val->location_id : "",
                    'City name'           =>  $val->city_name ? $val->city_name : "",
                    'State name'          =>  $val->state_name ? $val->state_name : "",
                    'created_at'          =>  $val->created_at ? date('Y-m-d',strtotime($val->created_at)) : "",
                ];
            }

            // echo "<pre>"; print_r($data); die();
            if (!File::exists(public_path() . "/files")) {
                File::makeDirectory(public_path() . "/files");
            }

            $filename = public_path('files/' . $down_file_name . ".csv");
            $handle   = fopen($filename, 'w+');
            try{
                chmod($filename,0777);
            }catch(Exception $e){}
            fputcsv($handle, array(
                'User Id', 'Account Id', 'User name', 'Location Id', 'City name', 'State name'  
            ));
            foreach ($data as $row) {
                fputcsv($handle, array(
                    $row['User Id'], $row['Account Id'], $row['User name'], $row['Location Id'], $row['City name'], $row['State name']
                ));
            }
            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            return Response::download($filename, $down_file_name . ".csv", $headers);
        } else {
            flash('Unable to generate transaction csv file. Try again later')->error();
        }
        return redirect(route('admin.locations.index'));
    }

    public function mergeLocation(Request $request){
        $data = $request->all();
        $selectedLocationIds = '';
        if(isset($data['location_ids']) && !empty($data['location_ids'])){
            $selectedLocationIds = Crypt::decrypt($data['location_ids']);
        }
        $locations = Location::with('locationTransDefault')->whereIsActive('y')->get();
        return view('admin.pages.locations.merge',compact('locations','selectedLocationIds'))->with(['custom_title' => 'Merge Location']);
    }

    public function mergeselectedlocations(MergeLocationRequest $request){

        $locationData = $request->all();
        try{

        $fromLocation = $locationData['from_location'];
        $toLocation = $locationData['location_id'];
        $array_without_ToLocation = array_values(array_diff($fromLocation, array($toLocation)));
        $locationMerged=false;
        for($i=0;$i<count($array_without_ToLocation);$i++){
            
            $currentUserSettedLocation = User::where('location_id',$array_without_ToLocation[$i])->get();
            $currentUserInterestLocation = Interest::where('location_id',$array_without_ToLocation[$i])->get();
            if($currentUserSettedLocation->count() > 0){
                User::where('location_id',$array_without_ToLocation[$i])->update([
                    'location_id'=>$toLocation,
                    'discover_location_id'=>$toLocation
                ]);
            }

            if($currentUserInterestLocation->count() > 0){
                Interest::where('location_id',$array_without_ToLocation[$i])->update([
                    'location_id'=>$toLocation
                ]);
            }

            // $delLocationTrans = LocationTranslation::where('location_id',$array_without_ToLocation[$i])->update(['is_active'=>'n']);

            $delLocation = Location::where('id',$array_without_ToLocation[$i])->update(['is_active'=>'n']);
            if($i == count($array_without_ToLocation)-1){

              $locationMerged=true;

            }
        }

        if($locationMerged){
            flash('Location merged successfully!')->success();
        }else{
            flash('Unable to merge location. Try again later')->error();
        }

        return redirect(route('admin.locations.duplicate-location'));

    } catch (\Exception $e) {
        // Add error log
        flash($e->getMessage())->error();
        return redirect(route('admin.locations.duplicate-location'));
    } 
   }

   public function duplicatelisting(Request $request){
    //   dd($request->all());
      return view('admin.pages.locations.duplicatelisting')->with(['custom_title' => 'Duplicate Locations']);
   }

   public function duplicateLocationlisting(Request $request){
    extract($this->DTFilters($request->all()));
        $records = [];
        DB::enableQueryLog();
        if ($search != '') {
            $sql = "SELECT a.*,locations.custom_id,locations.is_active,b.total,b.location_id as all_location_ids FROM location_translations a JOIN (SELECT name,state, COUNT(*) as total,GROUP_CONCAT(location_id) as location_id FROM location_translations JOIN locations ON locations.id = location_translations.location_id WHERE location_translations.locale = 'en' AND locations.is_active = 'y' AND name LIKE '%".$search."%' GROUP BY name,state HAVING count(*) > 1 ORDER BY name) as b ON a.name = b.name AND a.state = b.state AND a.locale = 'en' join locations ON a.location_id = locations.id  WHERE locations.is_active = 'y' GROUP BY a.name,a.state ORDER BY a.name";


            $limitsql =  " LIMIT ".$limit." OFFSET ".$offset.";";
            $locations = DB::select($sql.$limitsql);
            $totallocations = DB::select($sql);

        }else{
            
            $sql = "SELECT a.*,locations.custom_id,locations.is_active,b.total,b.location_id as all_location_ids FROM location_translations a JOIN (SELECT name,state, COUNT(*) as total,GROUP_CONCAT(location_id) as location_id FROM location_translations JOIN locations ON locations.id = location_translations.location_id WHERE location_translations.locale = 'en' AND locations.is_active = 'y' GROUP BY name,state HAVING count(*) > 1 ORDER BY name) as b ON a.name = b.name AND a.state = b.state AND a.locale = 'en' join locations ON a.location_id = locations.id  WHERE locations.is_active = 'y' GROUP BY a.name,a.state ORDER BY a.name";

            $limitsql =  " LIMIT ".$limit." OFFSET ".$offset.";";
            $locations = DB::select($sql.$limitsql);
            $totallocations = DB::select($sql);
        }
        
        // ->offset($offset)->limit($limit)
        
        $count = count($locations);
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = count($totallocations);
        $records['data'] = [];
        // dd($records);
        foreach ($locations as $location) {
            //dd($location);
            $params = [
                'checked'       =>  ($location->is_active == 'y' ? 'checked' : ''),
                'getaction'     =>  $location->is_active,
                'class'         =>  '',
                'id'            =>  $location->custom_id,
            ];

            $records['data'][] = [
                'id'            =>  $location->id,
                'name'          =>  $location->name ?? "",
                'state'         =>  $location->state ?? "",
                'countDuplicacy'=> $location->total,
                'action'        =>  view('admin.layouts.includes.mergelocation')->with(['custom_title' => 'Location', 'id' => $location->custom_id,'location_ids' => Crypt::encrypt($location->all_location_ids)], $location)->render(),
                'checkbox'      =>  view('admin.layouts.includes.checkbox')->with('id', $location->custom_id)->render(),
            ];
        }
        return $records;

   }
}
