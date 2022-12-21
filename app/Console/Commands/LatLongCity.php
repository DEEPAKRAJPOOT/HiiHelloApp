<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Location;
use App\Models\LocationTranslation;
use Illuminate\Support\Carbon;
use DB;
class LatLongCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:latlongcity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command to user lat and log to get city and state to assign location id to user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user_list = User::select('users.id as id','users.latitude as latitude','users.longitude as longitude','users.location_id as location_id','users.new_location_id as new_location_id','users.created_at as created_at')
                    ->where("new_location_id","n")
                    ->whereNotNull("latitude")
                    ->whereNotNull("longitude")
                    ->where('created_at','>=','2022-12-01')
                    ->where("created_at",'<=','2022-12-15')
                    ->limit(100)
                    ->get();
        // echo "<pre>"; print_r($user_list->toArray()); die();
        if (count($user_list) > 0) {
            foreach ($user_list as $key => $val) {
                // again check for latitude & longitude not empty
                if (!empty($val->latitude) && !empty($val->longitude)) {
                    // call google gecode api and get city and state name
                    $res = $this->get_city_name($val->latitude,$val->longitude);

                    // check city and state not empty
                    if (!empty($res) && !empty($res['city']) && !empty($res['state'])) {
                        // if already exist city and state then get id and update user location id
                        $locationTranslation = LocationTranslation::where('name',$res['city'])->where('state',$res['state'])->where('locale','en')->first();
                        if (!empty($locationTranslation)) {
                            $location_id = $locationTranslation->location_id;
                            
                            // update location table for city is used some one users
                            Location::where('id',$location_id)->update([ 
                                'is_used' =>  'y',
                            ]);

                            // update loction translate table location name and state update
                            LocationTranslation::where('location_id',$val->location_id)->where('locale','en')->update([ 
                                'name' =>  $res['city'],
                                'state' =>  $res['state'],
                            ]);
                        }
                        else
                        {
                            // if city and state not exits then create new
                            $location = new Location();        
                            $location->custom_id = getUniqueString('locations');  
                            $location->is_used   = 'y';  
                            $location->save();

                            $location_id = $location->id;

                            $LocationTranslation = new LocationTranslation();
                            $LocationTranslation->locale = 'en';  
                            $LocationTranslation->location_id = $location_id;  
                            $LocationTranslation->name = $res['city'];  
                            $LocationTranslation->state = $res['state'];  
                            $LocationTranslation->save();
                        }

                        // update user table location id
                        User::where('id',$val->id)->update([ 
                            'location_id' =>  $location_id,
                            'new_location_id' =>  'y',
                        ]);
                    }
                    else
                    {
                        // update user table location id
                        User::where('id',$val->id)->update([ 
                            'new_location_id' =>  'T',
                        ]);
                    }
                }
            }
        }
    }

    function get_city_name($lat,$long){
        $apiKey = 'AIzaSyDInVSLHXa1FXO3p7kgA7B_TK9L71tZbW8';
        $latlng = $lat.','.$long;
        $result = [];

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latlng."&sensor=true&key=".$apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
        $responseJson = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responseJson);
        if (!empty($response) && !empty($response->results[0]->address_components)) {
            foreach ($response->results[0]->address_components as $key => $value) {
                if ($value->types[0] == "administrative_area_level_3") {
                    $result['city'] = $value->long_name;
                }
                if ($value->types[0] == "administrative_area_level_1") {
                    $result['state'] = $value->long_name;
                }
            }
            return $result;
        }
    }
}
