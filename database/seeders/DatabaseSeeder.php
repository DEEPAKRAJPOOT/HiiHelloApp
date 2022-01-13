<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
// use Database\Seeders\SectionsTableSeeder;
// use Database\Seeders\RoleTableSeeder;
// use Database\Seeders\AdminsTableSeeder;
// use Database\Seeders\CountrySeeder;
// use Database\Seeders\StatesSeeder;
// use Database\Seeders\CitiesSeeder;
// use Database\Seeders\CmsPageSeeder;
// use Database\Seeders\SettingSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SectionsTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CountryTranslationSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(CitiesSeeder::class);
        $this->call(CmsPageSeeder::class);
        $this->call(CmsPageTranslationSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(LanguageSeeder::class);
    }
}
