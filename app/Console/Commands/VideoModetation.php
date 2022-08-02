<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Sightengine\SightengineClient;
use Illuminate\Support\Facades\Storage;
use App\Models\UserDetail;
use App\Http\Traits\FirebaseTrait;
use App\Jobs\NotificationJob;

class VideoModeration extends Command
{
    use FirebaseTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:moderation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to check the nudity content in video using third party api called sightengine & remove video from the database.';

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
        $message = "No Moderation Video Found.";
        try{
            UserDetail::select('id','user_id','video','is_verified')
                    ->with('user:id,custom_id')
                    ->whereNotNull('video')
                    ->where('is_verified','n')
                    ->chunk(100, function($media_videos) {
                if($media_videos->isNotEmpty()){
                    $need_to_notify = false;

                    foreach($media_videos as $media_video){ 

                        if(!empty($media_video->video)){
                            $media_photo = generateURL($media_video->video);
                            $user_id = $media_video->user ? $media_video->user->id : "";

                            if(!empty($media_photo)){
                                $safe_media_video = $this->checkVideoModeration($media_photo, $media_video->user);

                                if($safe_media_video == true){
                                    $media_video->is_verified = 'y';
                                    $media_video->save();
                                }else{
                                    // IF NOT SAFE
                                    if( Storage::exists($media_video->video) ) { Storage::delete($media_video->video); }
                                    $media_video->delete();

                                    $need_to_notify = true;
                                }

                                $message = 'User Id : '.$user_id.' media videos checked successfully !!!';
                            }
                        }
                    }
                    
                    // Notify User
                    if($need_to_notify && $media_video->user){
                        $this->sendVideoAlertNotification($media_video->user);
                    }
                }
            });
        } catch (\Exception $e) {
            // Add error log
            $file = 'video_moderation';
            $imgModerationLog = new Logger($file);
            $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $imgModerationLog->error($file, ['error' => $e->getMessage()]);
        }

        $this->info($message);
        return $message;
    }

    function checkVideoModeration($video_path, $user)
    {
        try{
            $api_url        =   config('utility.video_moderation.api_url');
            $api_user       =   config('utility.video_moderation.api_user');
            $api_secret     =   config('utility.video_moderation.api_secret');
    
            // Nudity Values
            $row_value      =   config('utility.video_moderation.row_value');
            $partial_value  =   config('utility.video_moderation.partial_value');
            $safe_value     =   config('utility.video_moderation.safe_value');

            $models         =   'nudity'; // We can also pass using comma values if we have multiple models
            // $models         =   "nudity,text"; // We can also pass using comma values if we have multiple models
            // $models         =   "nudity,text,properties"; // We can also pass using comma values if we have multiple models
            // $models         =   "nudity,text,properties,face-attributes"; // We can also pass using comma values if we have multiple models
            $safe_video     =   true;

            $client     =   new \GuzzleHttp\Client();
            $file       =   fopen($video_path, 'r');
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
                if($output->data && $output->data->frames){

                    if( count($output->data->frames) > 0){
                        foreach($output->data->frames as $frame){

                            // Check Nudity
                            if($frame->nudity){
                                $row            =   $frame->nudity->raw;
                                $safe           =   $frame->nudity->safe;
                                $partial        =   $frame->nudity->partial;

                                $row_condition      =   $row > $row_value;
                                $partial_condition  =   $partial > $partial_value;
                                $safe_condition     =   $safe < $safe_value;


                                if($row_condition || $partial_condition || $safe_condition){
                                    $safe_video = false; // nude video
                                }
                            }
                        }
                    }
                }
            }

            return $safe_video;

        } catch (\Exception $e) {
            // Add error log
            $file = 'video_moderation';
            $imgModerationLog = new Logger($file);
            $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $imgModerationLog->error($file, ['error' => $e->getMessage()]);
        }
    }

    // Send Notification
    function sendVideoAlertNotification($user){
        $notification = [
            'custom_id'     =>  getUniqueString('notifications'),
            'key'           =>  'user_id',
            'value'         =>  $user->custom_id,
            'user_id'       =>  $user->id,
            'image'         =>  '',
            'title'         =>  trans('api.notify_message.video_moderation.title'),
            'message'       =>  trans('api.notify_message.video_moderation.message'),
            'type'          =>  config('utility.notification.type.video_moderation'),
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
