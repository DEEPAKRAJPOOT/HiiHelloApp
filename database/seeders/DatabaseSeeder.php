<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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
        $this->call(ProfileDetailSeeder::class);
        $this->call(ProfileDetailTranslationSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(LocationTranslationSeeder::class);
        $this->call(InterestSeeder::class);
        $this->call(InterestTranslationSeeder::class);
    }
}
