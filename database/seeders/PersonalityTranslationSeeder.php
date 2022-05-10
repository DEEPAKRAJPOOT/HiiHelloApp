<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\PersonalityTranslation;

class PersonalityTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        PersonalityTranslation::truncate();
        Schema::enableForeignKeyConstraints();

        $personality_translations = array(
            array('locale' => 'en', 'personality_id' => 1, 'title' => 'The Commander', 'description' => 'Strategic leader and motivated to organize change', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 2, 'title' => 'The Mastermind', 'description' => 'Analytical problem-solvers and eager to improve systems and processes', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 3, 'title' => 'The Visionary', 'description' => 'Inspired innovators and seeking new solutions to challenging problems', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 4, 'title' => 'The Architect', 'description' => 'Philosophical innovators and fascinated by logical analysis', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 5, 'title' => 'The Teacher', 'description' => 'Idealist organizers and driven to do what is best for humanity', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 6, 'title' => 'The Counselor', 'description' => 'Creative nurturers and driven by a strong sense of personal integrity', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 7, 'title' => 'The Champion', 'description' => 'People-centered creators and motivated by possibilities and potential', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 8, 'title' => 'The Healer', 'description' => 'Imaginative idealists and guided by their own values and beliefs', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 9, 'title' => 'The Supervisor', 'description' => 'Hardworking traditionalists and taking charge to get things done', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 10, 'title' => 'The Inspector', 'description' => 'Responsible organizers and driven to create order out of chaos', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 11, 'title' => 'The Provider', 'description' => 'Conscientious helpers and dedicated to their duties to others', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 12, 'title' => 'The Protector', 'description' => 'Industrious caretakers and loyal to traditions and institutions', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 13, 'title' => 'The Dynamo', 'description' => 'Energetic thrillseekers and ready to push boundaries and dive into action', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 14, 'title' => 'The Craftsperson', 'description' => 'Observant troubleshooters and solving practical problems', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'personality_id' => 15, 'title' => 'The Entertainer', 'description' => 'Vivacious entertainers and loving life and charming those around them', 'created_at' => now(), 'updated_at' => now()),
            
            array('locale' => 'en', 'personality_id' => 16, 'title' => 'The Composer', 'description' => 'Gentle caretakers and enjoying the moment with low-key enthusiasm', 'created_at' => now(), 'updated_at' => now()),
        );

        PersonalityTranslation::insert($personality_translations);
    }
}
