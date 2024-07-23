<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\{UsersMongoose, User, ChatRoomMongoose, ChatMessageMongoose};


class MigrateUsersToMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate users from MySQL to MongoDB';

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
        UsersMongoose::truncate();
        // ChatRoomMongoose::truncate();
        // ChatMessageMongoose::truncate();
        // Retrieve data from MySQL
        $users = User::select('id','custom_id','profile_photo','language_id','is_active','last_online')
	      ->with(['userTranslation','language:id,lang_code'])->whereIn('email',['dayakargoud.bandari@saturdaytechnologies.io','krunal.vasundhara@gmail.com','rohan.vasundhara19@gmail.com'])->orWhereIn('contact_no',['7488618520','9205209548','9573791492','9326110491','9867175525'])->get();
        $userData=[];
        foreach($users as $user){
            $userData['user_id'] = $user->id;
            $userData['custom_id'] = $user->custom_id;
            $userData['full_name'] = $user->userTranslation->full_name?$user->userTranslation->full_name : "";
            $userData['profile_photo'] = $user->profile_photo;
            $userData['language_id'] = $user->language->id?$user->language->id : "";
            $userData['lang_code'] = $user->language->lang_code?$user->language->lang_code:"";
            $userData['is_active'] = ($user->is_active == 'y')?true:false;
            $userData['last_online'] = $user->last_online;
            $userData['deleted_at'] = NULL;
            $usersSaved = UsersMongoose::firstOrCreate($userData);
        }

        $this->info('Users migrated successfully.');
    }
}
