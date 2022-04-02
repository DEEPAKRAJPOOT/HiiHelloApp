<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Location::truncate();
        Schema::enableForeignKeyConstraints();

        $locatios = array(
            array('custom_id' => getUniqueString('locations'), 'created_at' => now(), 'updated_at' => now()),
        );

        Location::insert($locatios);
    }
}
