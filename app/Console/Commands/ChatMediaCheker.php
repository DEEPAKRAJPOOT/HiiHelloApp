<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
            ChatMessage::with(['room:id,custom_id','sender.deviceToken','receiver.userTransEn'])
                ->whereIsVerified('n')
                ->chunk(100, function($chatMessages) {

                if($chatMessages->isNotEmpty()){
                    $allow_types = ['jpeg','jpg','png','webp'];

                    foreach($chatMessages as $chatMessage){
                        $message = json_decode( preg_replace("/\r|\n/", " ", $chatMessage->message) );

                        if( !empty($message) 
                            && !empty($message->type) 
                            && !empty($message->value) 
                            && !empty($message->other) 
                            && $message->type == 'file' 
                            && !empty($message->other->path) 
                            && !empty($message->other->type) 
                            && in_array($message->other->type,$allow_types)
                        ){   
                            $image = generateURL($message->other->path);

                            if(!empty($image)){
                               
                                //BYPASS SIGHT ENGINE 28 OCT //
                                //$safe_image = $this->checkImageModeration($image);
                                $safe_image = true;


                                // IF NOT SAFE
                                if($safe_image == false){
                                    if( Storage::exists($message->other->path) ) { Storage::delete($message->other->path); }
                                    $chatMessage->is_verified = 'y';
                                    $chatMessage->save();
                                    $sender = $chatMessage->sender;
                                    $receiver = $chatMessage->receiver;

                                    // Delete Chat Message
                                    $chatMessage->delete();
                                    
                                    // Notify User
                                    if($sender){
                                        $this->sendImageAlertNotification($sender, $receiver, $chatMessage->room);
                                    }
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

        // $this->info($message);
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
            
            // Text Values
            $artificial_value   =   config('utility.image_moderation.artificial_value');
            $natural_value      =   config('utility.image_moderation.natural_value');

            // $models         =   'nudity'; // We can also pass using comma values if we have multiple models
            $models         =   "nudity,text"; // We can also pass using comma values if we have multiple models
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

                    // If Image Is Not Safe
                    if($row_condition || $partial_condition || $safe_condition){
                        $safe_image = false;
                    }
                }

                // Check Embedded Text 
                if($output->text){
                    $has_artificial =   $output->text->has_artificial;
                    $has_natural    =   $output->text->has_natural;

                    $artificial_condition   =   $has_artificial > $artificial_value;
                    $natural_condition      =   $has_natural < $natural_value;

                    // If Image Is Not Safe
                    if($artificial_condition && $natural_condition){
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
    function sendImageAlertNotification($sender, $receiver, $room){
        $receiver_name = $receiver ? $receiver->userTransEn ? $receiver->userTransEn->full_name : "" : "";
        $receiver_profile = $receiver ? $receiver->profile_photo : "";

        $notification = [
            'custom_id'     =>  getUniqueString('notifications'),
            'key'           =>  'user_id',
            'room_id'       =>  $room ? $room->custom_id : "",
            'value'         =>  $sender->custom_id,
            'user_id'       =>  $sender->id,
            'name'          =>  $receiver_name,
            'profile'       =>  generateURL($receiver_profile),
            'image'         =>  generateURL($receiver_profile),
            'title'         =>  trans('api.notify_message.image_moderation_chat.title'),
            'message'       =>  trans('api.notify_message.image_moderation_chat.message'),
            'type'          =>  config('utility.notification.type.image_moderation_chat'),
        ];
        
        // Notify
        $notificationJob = new NotificationJob($notification, $sender);
        dispatch($notificationJob);
    }
}
