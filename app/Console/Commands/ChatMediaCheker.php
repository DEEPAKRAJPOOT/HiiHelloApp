<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\ChatMessage;
use App\Jobs\NotificationJob;

class ChatMediaCheker extends Command
{    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:media-moderation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to check the nudity media content in chat messages & delete from the database.';

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
        $message = "No Chat Moderation Image Found.";

        try{
            ChatMessage::with(['room:id,custom_id','sender.deviceToken'])
                ->whereIsVerified('n')
                ->chunk(100, function($chatMessages) {

                if($chatMessages->isNotEmpty()){
                    foreach($chatMessages as $chatMessage){
                        $message = json_decode( preg_replace("/\r|\n/", " ", $chatMessage->message) );

                        if(!empty($message) && !empty($message->type) && !empty($message->value) && !empty($message->other) 
                            && $message->type == 'file' && $message->value == 'Image' && !empty($message->other->path) ){   
                            $image = generateURL($message->other->path);
                            if(!empty($image)){
                                $safe_main_image = $this->checkImageModeration($image);

                                // IF NOT SAFE
                                if($safe_image == false){
                                    if( Storage::exists($message->other->path) ) { Storage::delete($message->other->path); }
                                    $chatMessage->is_verified = 'y';
                                    $chatMessage->save();

                                    // Notify User
                                    $sender = $chatMessage->sender;
                                    if($sender){
                                        $this->sendImageAlertNotification($sender, $chatMessage->room);
                                    }

                                    // Delete Chat Message
                                    $chatMessage->delete();
                                }
                            }
                        }
                        $chatMessage->is_verified = 'y';
                        $chatMessage->save();

                        $message = 'Chat image checked successfully !!!';
                    }
                }
            });
        } catch (\Exception $e) {
            // Add error log
            $file = 'chat_media_moderation';
            $chatImgModerationLog = new Logger($file);
            $chatImgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $chatImgModerationLog->error($file, ['error' => $e->getMessage()]);
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
            $file = 'chat_media_moderation';
            $chatImgModerationLog = new Logger($file);
            $chatImgModerationLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
            $chatImgModerationLog->error($file, ['error' => $e->getMessage()]);
        }
    }

    // Send Notification
    function sendImageAlertNotification($user, $room){
        $notification = [
            'custom_id'     =>  getUniqueString('notifications'),
            'key'           =>  'user_id',
            'room_id'       =>  $room ? $room->custom_id : "",
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
}
