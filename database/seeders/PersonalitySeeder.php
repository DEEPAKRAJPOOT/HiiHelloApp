<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Personality;

class PersonalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Personality::truncate();
        Schema::enableForeignKeyConstraints();
    
        $personalities = array(
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
            array('custom_id' => getUniqueString('personalities'), 'image' => NULL, 'is_active' => 'y', 'created_at' => now(), 'updated_at' => now()),
        );

        Personality::insert($personalities);
    }
}
