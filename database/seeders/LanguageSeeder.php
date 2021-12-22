<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Language::truncate();
        Schema::enableForeignKeyConstraints();

        $languages = [
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'English',
                'lang_code'     => 'en',
                'hint'          => 'English',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'हिंदी',
                'lang_code'     => 'hi',
                'hint'          => 'Hindi',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'தமிழ்',
                'lang_code'     => 'ta',
                'hint'          => 'Tamil',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'मराठी',
                'lang_code'     => 'mr',
                'hint'          => 'Marathi',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'বাংলা',
                'lang_code'     => 'bn',
                'hint'          => 'Bengali',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'ગુજરાતી',
                'lang_code'     => 'gu',
                'hint'          => 'Gujarati',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'ಕನ್ನಡ',
                'lang_code'     => 'kn',
                'hint'          => 'Kannada',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'മലയാളം',
                'lang_code'     => 'ml',
                'hint'          => 'Malayalam',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'भोजपुरी',
                'lang_code'     => 'bho',
                'hint'          => 'Bhojpuri',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'पंजाबी',
                'lang_code'     => 'pa',
                'hint'          => 'Punjabi',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('languages'),
                'language'      => 'తెలుగు',
                'lang_code'     => 'te',
                'hint'          => 'Telugu',
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
        ];

        try{
            DB::table('languages')->insert($languages);
        }catch(\Illuminate\Database\QueryException $ex){
            dd($ex->getMessage());
        }
    }
}
