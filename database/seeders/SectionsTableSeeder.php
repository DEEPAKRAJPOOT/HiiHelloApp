<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Section::truncate();
        Schema::enableForeignKeyConstraints();
        $sections = [
                [
                    'name'          =>  'Dashboard',
                    'icon'          =>  'icon-home',
                    'image'         =>  '',
                    'icon_type'     =>  'line-icons',
                    'sequence'      =>  1,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Users',
                    'icon'          =>  'icon-users ',
                    'image'         =>  '',
                    'icon_type'     =>  'line-icons',
                    'sequence'      =>  2,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Role Management',
                    'icon'          =>  'fab fa-black-tie',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      =>  3,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Passions Management',
                    'icon'          =>  'fas fa-drum',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      =>  4,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Countries',
                    'icon'          =>  'fas fa-flag',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      =>  5,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Interests',
                    'icon'          =>  'fas fa-icons',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      =>  6,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Locations',
                    'icon'          =>  'fas fa-map-marker-alt',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      =>  7,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'States',
                    'icon'          =>  'la la-flag',
                    'image'         =>  '',
                    'icon_type'     =>  'other',
                    'sequence'      =>  8,
                    'is_active'     => 'n',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          =>  'Cities',
                    'icon'          =>  'la la-city',
                    'image'         =>  '',
                    'icon_type'     =>  'other',
                    'sequence'      =>  9,
                    'is_active'     => 'n',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          => 'CMS',
                    'icon'          =>  'fas fa-book',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      => 10,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
                [
                    'name'          => 'Site Configuration',
                    'icon'          =>  'icon-settings',
                    'image'         =>  '',
                    'icon_type'     =>  'font-awesome',
                    'sequence'      => 11,
                    'is_active'     => 'y',
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                ],
        ];
        Section::insert($sections);
    }
}
