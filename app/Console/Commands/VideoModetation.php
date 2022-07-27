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

class VideoModetation extends Command
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
        // $message = "No Moderation Video Found.";
        // try{
        //     UserDetail::select('id','user_id','video','is_verified')
        //             ->with('user:id,custom_id,profile_photo,gender')
        //             ->whereNotNull('video')
        //             ->where('is_verified','n')
        //             ->chunk(100, function($media_videos) {
        //         if($media_videos->isNotEmpty()){
        //             $need_to_notify = false;

        //             foreach($media_videos as $media_video){ 

        //                 if(!empty($media_video->video)){
        //                     $media_photo = generateURL($media_video->video);
        //                     $user_id = $media_video->user ? $media_video->user->id : "";

        //                     if(!empty($media_photo)){
        //                         dd($media_photo);
        //                         $safe_media_video = $this->checkVideoModeration($media_photo, $media_video->user);
                    
        //                         if($safe_media_video == true){
        //                             $media_video->is_verified = 'y';
        //                             $media_video->save();
        //                         }else{
        //                             // IF NOT SAFE
        //                             if( Storage::exists($media_video->video) ) { Storage::delete($media_video->video); }
        //                             $media_video->delete();

        //                             $need_to_notify = true;
        //                         }

        //                         $message = 'User Id : '.$user_id.' media videos checked successfully !!!';
        //                     }
        //                 }
        //             }
                    
        //             // Notify User
        //             if($need_to_notify && $media_video->user){
        //                 $this->sendVideoAlertNotification($media_video->user);
        //             }
        //         }
        //     });
        // } catch (\Exception $e) {
        //     // Add error log
        //     $file = 'video_moderation';
        //     $imgModerationLog = new Logger($file);
        //     $imgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
        //     $imgModerationLog->error($file, ['error' => $e->getMessage()]);
        // }

        // $this->info($message);
        // return $message;
    }
}
