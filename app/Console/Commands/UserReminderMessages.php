<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Models\ChatMessage;
use App\Models\User;

class UserReminderMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:remindermessages';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'This Command is used to send profile related reminders to users';

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
        $this->profilePercentageReminder();
        $this->accountVerificationReminder();
    }

    private function profilePercentageReminder(){
        $past_15_days = now()->subDays(15)->format('Y-m-d');
        $low_profile_percentage_users = User::whereIsActive('y')
        ->whereIsTestUser('n')
        ->where('profile_percentage','<',20)
        ->whereDoesntHave('chatMessagesReceived',function($query)use($past_15_days){
            $query->where('room_id',config('utility.chat.system_chat_room'))
            ->where('message','like','"system_message_type":"reminder_percentage"')
            ->where('created_at','>=',$past_15_days);
        });
        $system_chat_room_id = config('utility.chat.system_chat_room');
        $system_user_id = config('utility.system.system_user_id');
        $low_profile_percentage_users->chunk(500,function($user_chunk)use($system_chat_room_id,$system_user_id){
            $bulk_insert = [];
            foreach($user_chunk as $user){
                $bulk_insert[] = [
                    'custom_id'     =>  getUniqueString('chat_messages'),
                    'room_id'       =>  $system_chat_room_id,
                    'sender_id'     =>  $system_user_id,
                    'receiver_id'   =>  $user->id,
                    'message'       =>  json_encode([
                        'type'      =>  'text',
                        'value'     =>  "Your dating profile is your digital introduction to the world of Hi Hello, and making it perfect is the key to attracting meaningful connections. Here's why investing time in creating an appealing profile and choosing the right profile photo is essential. ".PHP_EOL.PHP_EOL."First impressions matter, and your profile is your chance to make a lasting one. Showcasing your true personality, interests, and values through a well-crafted bio and thoughtfully chosen photos can help you stand out from the crowd. Be authentic, be yourself, and let your uniqueness shine through. ".PHP_EOL.PHP_EOL."When it comes to your profile photo, remember that a picture is worth a thousand words. Choose a clear, high-quality image that accurately represents you. Smile genuinely, make eye contact, and let your warmth radiate. Avoid heavily filtered or overly posed photos, as they may give off an inauthentic vibe. Remember, you want potential matches to see the real you. ".PHP_EOL.PHP_EOL."A well-crafted profile and an appealing profile photo not only catch the attention of others but also increase your chances of finding compatible matches. Think of your profile as a conversation starter, an invitation for others to get to know you better. Give them a glimpse into your life, hobbies, and passions. Show that you're open to new experiences and genuine connections. ".PHP_EOL.PHP_EOL."So take the time to perfect your dating profile. Be genuine, be engaging, and be yourself. Let your personality shine through every word and image. By putting effort into creating an enticing profile, you'll attract like-minded individuals who are excited to connect with the real you.",
                        'other'     =>  (object)[],
                        'system_message_type'  => 'reminder_percentage'
                    ]),
                    'created_at'    =>  now(),
                    'updated_at'    =>  now()
                ];
            }
            DB::beginTransaction();
            try{
                ChatMessage::where('room_id',config('utility.chat.system_chat_room'))->whereIn('receiver_id',array_column($bulk_insert,'receiver_id'))->where('message','like','"system_message_type":"reminder_percentage"')->delete();
                ChatMessage::insert($bulk_insert);
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
            }
        });
    }

    private function accountVerificationReminder(){
        $past_15_days = now()->subDays(15)->format('Y-m-d');
        $unverified_account_users = User::whereIsActive('y')
        ->whereIsTestUser('n')
        ->where(function($query){
            $query->where('email_verified_at',null)
            ->orWhere('verify_photo_status','!=','verified')
            ->orWhere('contact_verified_at',null);
        })
        ->whereDoesntHave('chatMessagesReceived',function($query)use($past_15_days){
            $query->where('room_id',config('utility.chat.system_chat_room'))
            ->where('message','like','"system_message_type":"reminder_verification"')
            ->where('created_at','>=',$past_15_days);
        });
        $system_chat_room_id = config('utility.chat.system_chat_room');
        $system_user_id = config('utility.system.system_user_id');
        $unverified_account_users->chunk(500,function($user_chunk)use($system_chat_room_id,$system_user_id){
            $bulk_insert = [];
            foreach($user_chunk as $user){
                $bulk_insert[] = [
                    'custom_id'     =>  getUniqueString('chat_messages'),
                    'room_id'       =>  $system_chat_room_id,
                    'sender_id'     =>  $system_user_id,
                    'receiver_id'   =>  $user->id,
                    'message'       =>  json_encode([
                        'type'      =>  'text',
                        'value'     =>  "Account verification is a crucial step towards creating a safe and secure community within Hi Hello. By verifying your account, you ensure that you are connecting with real, genuine individuals who share the same commitment to authenticity. Take a moment to complete the verification process and unlock the full potential of our platform. Ensure you complete all 3 levels of verification in the platform – phone, email and photo. ".PHP_EOL.PHP_EOL."Verified users experience a higher level of trust and have access to a vibrant community of like-minded individuals. Join us in building a secure and meaningful space where genuine connections flourish. ".PHP_EOL.PHP_EOL."Verify your account today and open the door to a world of possibilities.",
                        'other'     =>  (object)[],
                        'system_message_type'  => 'reminder_verification'
                    ]),
                    'created_at'    =>  now(),
                    'updated_at'    =>  now()
                ];
            }
            DB::beginTransaction();
            try{
                ChatMessage::where('room_id',config('utility.chat.system_chat_room'))->whereIn('receiver_id',array_column($bulk_insert,'receiver_id'))->where('message','like','"system_message_type":"reminder_verification"')->delete();
                ChatMessage::insert($bulk_insert);
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
            }
        });
    }
}