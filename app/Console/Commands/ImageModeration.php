<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Sightengine\SightengineClient;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ImageModeration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:moderation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to check the nudity content of images using third party api called sightengine & remove thoes images from the database.';

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
        $message = "No Moderation Image Found.";
        try{
            $users = User::select('id','profile_photo','is_media_checked')
                                ->with('userDetails:id,user_id,image')->where('is_media_checked','n')->get();

            if($users->isNotEmpty()){
                foreach($users as $user){
                    $profile_photo = generateURL($user->profile_photo);

                    // Main Image
                    if(!empty($profile_photo)){
                        $safe_main_image = $this->checkImageModeration($profile_photo);
                        
                        // IF NOT SAFE
                        if($safe_main_image == false){
                            if( Storage::exists($user->profile_photo) ) { Storage::delete($user->profile_photo); }
                            $user->profile_photo = NULL;
                            $user->save();
                        }
                        $message = 'User Id : '.$user->id.' images checked successfully !!!';
                    }

                    // Media Images
                    if($user->userDetails->isNotEmpty()){
                        foreach($user->userDetails as $user_detail){
                            if(!empty($user_detail->image)){
                                $media_photo = generateURL($user_detail->image);

                                if(!empty($media_photo)){
                                    $safe_media_image = $this->checkImageModeration($media_photo);
                        
                                    // IF NOT SAFE
                                    if($safe_media_image == false){
                                        if( Storage::exists($user_detail->image) ) { Storage::delete($user_detail->image); }
                                        $user_detail->delete();
                                    }
                                    $message = 'User Id : '.$user->id.' images checked successfully !!!';
                                }
                            }
                        }
                    }

                    $user->is_media_checked = 'y';
                    $user->save();
                }
            }
        } catch (\Exception $e) {
            // Add error log
            $file = 'image_moderation';
            $iqTrackingLog = new Logger($file);
            $iqTrackingLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $iqTrackingLog->error($file, ['error' => $e->getMessage()]);
        }

        $this->info($message);
        return $message;
    }

    function checkImageModeration($image_path)
    {
        try{
            $api_url        =   config('utility.image_moderation.api_url');
            $api_user       =   config('utility.image_moderation.api_user');
            $api_secret     =   config('utility.image_moderation.api_secret');
            $row_value      =   config('utility.image_moderation.row_value');
            $partial_value  =   config('utility.image_moderation.partial_value');
            $safe_value     =   config('utility.image_moderation.safe_value');
            $models         =   'nudity'; // We can also pass array if we have multiple models
            $safe_image     =   true;

            $client     =   new \GuzzleHttp\Client();
            $file       =   fopen($image_path, 'r');
            $response   =   $client->request('POST', $api_url, 
                            [
                                'query' => [
                                    'api_user'      =>  $api_user,
                                    'api_secret'    =>  $api_secret,
                                    'models'        =>  $models
                                ],
                                'multipart' => [
                                    [
                                        'name'      =>  'media',
                                        'contents'  =>  $file
                                    ]
                                ]
                            ]); 

            $output = json_decode($response->getBody());
            if($output->status == 'success'){
                if($output->nudity){
                    $row            =   $output->nudity->raw;
                    $safe           =   $output->nudity->safe;
                    $partial        =   $output->nudity->partial;

                    $row_condition      =   $row > $row_value;
                    $partial_condition  =   $partial > $partial_value;
                    $safe_condition     =   $safe < $safe_value;

                    // If Image Is Not Safe
                    if($row_condition || $partial_condition || $safe_condition){
                        $safe_image = false;
                    }
                }
            }

            return $safe_image;

        } catch (\Exception $e) {
            // Add error log
            $file = 'image_moderation';
            $iqTrackingLog = new Logger($file);
            $iqTrackingLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $iqTrackingLog->error($file, ['error' => $e->getMessage()]);
        }
    }

    // Information As Per Documentation
    // LINK :: https://sightengine.com/docs/nsfw-detection-model

    // 1) RAW NUDITY
    //    ->    Decimal between 0 and 1. Images with a value close to 1 are images with a high probability of containing raw nudity while images with a probability closer to 0 have a lower probability of containing raw nudity.

    // 2) PARTIAL NUDITY
    //     ->  Decimal between 0 and 1. Images with a value close to 1 are images with a high probability of containing partial nudity while images with a probability closer to 0 have a lower probability of containing partial nudity.
    //         If this value is larger than nudity.raw nudity.safe then a nudity.partial_tag field will be added to describe the type of partial nudity found.

    // 3) SAFE
    //     ->  Decimal between 0 and 1. Images with a value close to 1 are images with a high probability of being safe (i.e. no nudity) while images with a probability closer to 0 have a lower probability of being safe.

}   
