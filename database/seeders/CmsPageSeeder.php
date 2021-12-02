<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CmsPage::truncate();
        $pages = [
            [
                'edited_by' => 1,
                'title' => 'Home',
                'slug' => 'home',
                'description' => '<p>home</p>',
                'file' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'edited_by' => 1,
                'title' => 'About Us',
                'slug' => 'about-us',
                'description' => '<p>&nbsp; &nbsp; &nbsp;About Us</p>',
                'file' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'edited_by' => 1,
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'description' => '<p>&nbsp; &nbsp; &nbsp;Terms and Conditions</p>',
                'file' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'edited_by' => 1,
                'title' => 'Privacy',
                'slug' => 'privacy',
                'description' => '<p>&nbsp; &nbsp; &nbsp;Privacy</p>',
                'file' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
        ];
        DB::table('cms_pages')->insert($pages);
    }
}
