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

        // Define the users to exclude
        $excludedEmails = [
            'dayakargoud.bandari@saturdaytechnologies.io',
            'krunal.vasundhara@gmail.com',
            'rohan.vasundhara19@gmail.com'
        ];

        $excludedContacts = [
            '7488618520',
            '9205209548',
            '9573791492',
            '9326110491',
            '9867175525'
        ];

        // Retrieve data from MySQL, excluding specified users
        $users = User::select('id', 'custom_id', 'profile_photo', 'language_id', 'is_active', 'last_online')
            ->with(['userTranslation', 'language:id,lang_code'])
            ->whereNotIn('email', $excludedEmails)
            ->whereNotIn('contact_no', $excludedContacts)
            ->get();

        $userData = [];
        foreach($users as $user) {
            $userData = [
                'user_id' => $user->id,
                'custom_id' => $user->custom_id,
                'full_name' => $user->userTranslation->full_name ?? "",
                'profile_photo' => $user->profile_photo,
                'language_id' => $user->language->id ?? "",
                'lang_code' => $user->language->lang_code ?? "",
                'is_active' => ($user->is_active == 'y') ? true : false,
                'last_online' => $user->last_online,
                'deleted_at' => null,
            ];

            UsersMongoose::firstOrCreate($userData);
        }

        $this->info('Users migrated successfully.');
    }
}
