<?php

namespace Database\Seeders;

use App\Models\AppDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppDetail::truncate();
        $app_details = [
            [
                'custom_id' =>  getUniqueString('app_details'),
                'constant' => 'verification_image_male',
                'value' =>  '',
                'hint' => 'Verification Sample Image For Male',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'custom_id' =>  getUniqueString('app_details'),
                'constant' => 'verification_video_male',
                'value' =>  '',
                'hint' => 'Verification Sample Video For Male',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'custom_id' =>  getUniqueString('app_details'),
                'constant' => 'verification_image_female',
                'value' =>  '',
                'hint' => 'Verification Sample Image For Female',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'custom_id' =>  getUniqueString('app_details'),
                'constant' => 'verification_video_female',
                'value' =>  '',
                'hint' => 'Verification Sample Video For Female',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ];

        $db = DB::table('app_details')->insert($app_details);
    }
}
