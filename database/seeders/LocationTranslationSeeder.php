<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\LocationTranslation;

class LocationTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        LocationTranslation::truncate();
        Schema::enableForeignKeyConstraints();

        $location_translatios = array(
            array('locale' => 'en', 'location_id' => 1, 'name' => 'Ahmedabad', 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'hi', 'location_id' => 1, 'name' => 'अहमदाबाद', 'created_at' => now(), 'updated_at' => now()),
        );

        LocationTranslation::insert($location_translatios);
    }
}
