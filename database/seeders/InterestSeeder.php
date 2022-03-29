<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Interest;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Interest::truncate();
        Schema::enableForeignKeyConstraints();

        $interests = array(
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => NULL, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 1, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 2, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 3, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 4, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 5, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 6, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 6, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 6, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 6, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 6, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 6, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 7, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 8, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),

            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('interests'), 'parent_id' => 9, 'location_id' => NULL, 'created_at' => now(), 'updated_at' => now()),
        );

        Interest::insert($interests);
    }
}
