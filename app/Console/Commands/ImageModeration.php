<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Sightengine\SightengineClient;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UserDetail;
use App\Http\Traits\FirebaseTrait;
use App\Jobs\NotificationJob;

class ImageModeration extends Command
{
    use FirebaseTrait;

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
            User::select('id','custom_id','profile_photo','is_media_checked')
                    ->where('is_media_checked','n')
                    ->chunk(100, function($users) {
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
                                $user->is_media_checked = 'y';
                                $user->save();

                                // Notify User
                                $this->sendImageAlertNotification($user);
                            }
                            $user->is_media_checked = 'y';
                            $user->save();

                            $message = 'User Id : '.$user->id.' main image checked successfully !!!';
                        }
                    }
                }
            });

            UserDetail::select('id','user_id','image','is_verified')
                    ->with('user:id,custom_id,profile_photo')
                    ->whereNotNull('image')
                    ->where('is_verified','n')
                    ->chunk(100, function($media_images) {
                if($media_images->isNotEmpty()){
                    $need_to_notify = false;

                    foreach($media_images as $media_image){ 
                        if(!empty($media_image->image)){
                            $media_photo = generateURL($media_image->image);
                            $user_id = $media_image->user ? $media_image->user->id : "";

                            if(!empty($media_photo)){
                                $safe_media_image = $this->checkImageModeration($media_photo);
                    
                                if($safe_media_image == true){
                                    $media_image->is_verified = 'y';
                                    $media_image->save();
                                }else{
                                    // IF NOT SAFE
                                    if( Storage::exists($media_image->image) ) { Storage::delete($media_image->image); }
                                    $media_image->delete();

                                    $need_to_notify = true;
                                }

                                $message = 'User Id : '.$user_id.' media images checked successfully !!!';
                            }
                        }
                    }
                    
                    // Notify User
                    if($need_to_notify && $media_image->user){
                        $this->sendImageAlertNotification($media_image->user);
                    }
                }
            });
        } catch (\Exception $e) {
            // Add error log
            $file = 'image_moderation';
            $imgModerationLog = new Logger($file);
            $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $imgModerationLog->error($file, ['error' => $e->getMessage()]);
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
            $imgModerationLog = new Logger($file);
            $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $imgModerationLog->error($file, ['error' => $e->getMessage()]);
        }
    }

    // Send Notification
    function sendImageAlertNotification($user){
        $notification = [
            'custom_id'     =>  getUniqueString('notifications'),
            'key'           =>  'user_id',
            'value'         =>  $user->custom_id,
            'user_id'       =>  $user->id,
            'image'         =>  '',
            'title'         =>  trans('api.notify_message.image_moderation.title'),
            'message'       =>  trans('api.notify_message.image_moderation.message'),
            'type'          =>  config('utility.notification.type.image_moderation'),
        ];

        // Notify
        $notificationJob = new NotificationJob($notification, $user);
        dispatch($notificationJob);
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
