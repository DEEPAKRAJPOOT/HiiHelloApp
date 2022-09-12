<?php

namespace Database\Seeders;

use App\Models\CmsPageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CmsPageTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        CmsPageTranslation::truncate();
        Schema::enableForeignKeyConstraints();

        $pages = [
            [
                'locale'            => 'en',
                'cms_page_id'       => 1,
                'title'             => 'About Us',
                'description'       => 'About Us',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'en',
                'cms_page_id'       => 2,
                'title'             => 'Terms and Conditions',
                'description'       => 'Terms and Conditions',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'en',
                'cms_page_id'       => 3,
                'title'             => 'Privacy',
                'description'       => 'Privacy',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'en',
                'cms_page_id'       => 4,
                'title'             => 'Community And Safety Guidelines',
                'description'       => 'Community And Safety Guidelines',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'hi',
                'cms_page_id'       => 1,
                'title'             => 'हमारे बारे में',
                'description'       => 'हमारे बारे में',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'hi',
                'cms_page_id'       => 2,
                'title'             => 'नियम और शर्तें',
                'description'       => 'नियम और शर्तें',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'hi',
                'cms_page_id'       => 3,
                'title'             => 'गोपनीयता',
                'description'       => 'गोपनीयता',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
            [
                'locale'            => 'hi',
                'cms_page_id'       => 4,
                'title'             => 'समुदाय और सुरक्षा दिशानिर्देश',
                'description'       => 'समुदाय और सुरक्षा दिशानिर्देश',
                'created_at'        => \Carbon\Carbon::now(),
                'updated_at'        => \Carbon\Carbon::now(),
            ],
        ];
        DB::table('cms_page_translations')->insert($pages);
    }
}
