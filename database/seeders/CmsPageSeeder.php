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
        Schema::disableForeignKeyConstraints();
        CmsPage::truncate();
        Schema::enableForeignKeyConstraints();

        $pages = [
            [
                'custom_id'     => getUniqueString('cms_pages'),
                'slug'          => 'about-us',
                'edited_by'     => 1,
                'hint'          => 'About Us',
                'file'          => null,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('cms_pages'),
                'slug'          => 'terms-and-conditions',
                'edited_by'     => 1,
                'hint'          => 'Terms and Conditions',
                'file'          => null,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('cms_pages'),
                'slug'          => 'privacy',
                'edited_by'     => 1,
                'hint'          => 'Privacy',
                'file'          => null,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('cms_pages'),
                'slug'          => 'community-and-safety',
                'edited_by'     => 1,
                'hint'          => 'Community And Safety Guidelines',
                'file'          => null,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
            [
                'custom_id'     => getUniqueString('cms_pages'),
                'slug'          => 'safety-tips',
                'edited_by'     => 1,
                'hint'          => 'Safety Tips',
                'file'          => null,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ],
        ];
        DB::table('cms_pages')->insert($pages);
    }
}
