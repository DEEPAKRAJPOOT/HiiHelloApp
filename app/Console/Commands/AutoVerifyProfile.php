<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Http\Traits\FirebaseTrait;
use App\Jobs\NotificationJob;

class AutoVerifyProfile extends Command
{
    use FirebaseTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:auto-verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to auto verify the profile using third party sightengine api to check verify image & video details.';

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
        $message = 'No pending profile verification found !!!';
        

        User::select('id','custom_id','verify_photo','verify_video','verify_photo_status','verify_video_status','verify_status')
                ->with(['deviceToken'])
                ->where(function($query) {
                    $query->where('verify_photo_status','under_review') 
                        ->orWhere('verify_video_status','under_review') 
                        ->orWhere('verify_status','under_review');
                })
                ->chunk(100, function($users) {
            if($users->isNotEmpty()){
                foreach($users as $user){

                    $verify_status      =   'unverified';
                    $photo_verified     =   false;
                    $video_verified     =   false;

                    
                    if($user->verify_photo_status == 'under_review'){
                        $verify_photo = generateURL($user->verify_photo);

                        if(!empty($verify_photo)){
                            $safe_verify_photo = $this->checkImageModeration($verify_photo);
                            
                            /*if($safe_verify_photo == true){
                                $photo_verified = true;
                                $user->verify_photo_status = 'verified';
                                $user->photo_verified_at = \Carbon\Carbon::now();
                            }else{
                                $photo_verified = false;
                                $user->verify_photo_status = 'unverified';
                                $user->photo_verified_at = NULL;
                            }
                            $user->save();
                            */
                        }
                    }elseif($user->verify_photo_status == 'verified'){
                        //$photo_verified = true;
                    }
                    
                    if($user->contactVerifyStatus() == 'verified' && $user->emailVerifyStatus() == 'verified' && $user->verify_photo_status == 'verified')
                    {
                        $photo_verified = true;
                    }

                    // if($user->verify_video_status == 'under_review'){
                    //     $verify_video = generateURL($user->verify_video);

                    //     if(!empty($verify_video)){
                    //         $safe_verify_video = $this->checkVideoModeration($verify_video);
                    //         if($safe_verify_video == true){
                    //             $video_verified = true;
                    //             $user->verify_video_status = 'verified';
                    //             $user->video_verified_at = \Carbon\Carbon::now();
                    //         }else{
                    //             $video_verified = false;
                    //             $user->verify_video_status = 'unverified';
                    //             $user->video_verified_at = NULL;
                    //         }
                    //         $user->save();
                    //     }
                    // }elseif($user->verify_video_status == 'verified'){
                    //     $video_verified = true;
                    // }

                    // if($photo_verified && $video_verified){
                    //     $verify_status = 'verified';
                    // }

                    if($photo_verified){
                        $verify_status = 'verified';
                    }

                    /*
                    if($photo_verified)
                    {
                        echo "Profile Verified True";
                    }
                    else
                    {
                        echo "Profile Verified false";
                    }
                    exit;
                    */

                    $user->verify_status = $verify_status;
                    $user->save();

                    // Notify User
                    $this->sendProfileVerifyNotification($user);
                        
                    $message = 'Profile verification details updated successfully.';
                    // $this->info($message);
                }
            }
        });

        return $message;
    }

    function checkImageModeration($image_path)
    {
        try{
            $api_url        =   config('utility.image_moderation.api_url');
            $api_user       =   config('utility.image_moderation.api_user');
            $api_secret     =   config('utility.image_moderation.api_secret');

            // Nudity Values
            $row_value      =   config('utility.image_moderation.row_value');
            $partial_value  =   config('utility.image_moderation.partial_value');
            $safe_value     =   config('utility.image_moderation.safe_value');

            $models         =   'nudity'; // We can also pass using comma values if we have multiple models
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
                // Check Nudity
                if($output->nudity){
                    $row            =   $output->nudity->raw;
                    $safe           =   $output->nudity->safe;
                    $partial        =   $output->nudity->partial;

                    $row_condition      =   $row > $row_value;
                    $partial_condition  =   $partial > $partial_value;
                    $safe_condition     =   $safe < $safe_value;

                    if($row_condition || $partial_condition || $safe_condition){
                        $safe_image = false; // nude image
                    }
                }
            }

            return $safe_image;

        } catch (\Exception $e) {
            // Add error log
            $file = 'image_moderation_auto_verify_profile';
            $imgModerationLog = new Logger($file);
            $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $imgModerationLog->error($file, ['error' => $e->getMessage()]);
        }
    }

    function checkVideoModeration($video_path)
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
            $file = 'video_moderation_auto_verify_profile';
            $imgModerationLog = new Logger($file);
            $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $imgModerationLog->error($file, ['error' => $e->getMessage()]);
        }
    }

    // Send Notification
    function sendProfileVerifyNotification($user){
        if ($user->verify_status != 'under_review') {
            if ($user->verify_status == 'verified') {
                $title      =   trans('api.notify_message.profile_verified.title');
                $message    =   trans('api.notify_message.profile_verified.message');
                $type       =   config('utility.notification.type.profile_verified');
            } else {
                $title      =   trans('api.notify_message.profile_not_verified.title');
                $message    =   trans('api.notify_message.profile_not_verified.message');
                $type       =   config('utility.notification.type.profile_not_verified');
            }

            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'user_id',
                'value'         =>  $user->id,
                'user_id'       =>  $user->id,
                'title'         =>  $title,
                'message'       =>  $message,
                'image'         =>  '',
                'type'          =>  $type,
            ];

             //09 SEP : STOP SENDING NOTIFICATION FOR PROFILE NOT VERIFIED
            // Notify
            if ($user->verify_status == 'verified') {
                $notificationJob = new NotificationJob($notification, $user);
                dispatch($notificationJob);
            }
        }

        // Notify Photo
        if ($user->verify_photo_status == 'unverified') {
            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'user_id',
                'value'         =>  $user->id,
                'user_id'       =>  $user->id,
                'title'         =>  trans('api.notify_message.verify_fail_photo.title'),
                'message'       =>  trans('api.notify_message.verify_fail_photo.message'),
                'image'         =>  '',
                'type'          =>  config('utility.notification.type.verify_fail_photo'),
            ];

            // Notify
            $notificationJob = new NotificationJob($notification, $user);
            dispatch($notificationJob);
        }

        // Notify Video
        if ($user->verify_video_status == 'unverified') {
            $notification = [
                'custom_id'     =>  getUniqueString('notifications'),
                'key'           =>  'user_id',
                'value'         =>  $user->id,
                'user_id'       =>  $user->id,
                'title'         =>  trans('api.notify_message.verify_fail_video.title'),
                'message'       =>  trans('api.notify_message.verify_fail_video.message'),
                'image'         =>  '',
                'type'          =>  config('utility.notification.type.verify_fail_video'),
            ];

            // Notify
            $notificationJob = new NotificationJob($notification, $user);
            dispatch($notificationJob);
        }
    }
}
