<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\SubscriptionPlanTranslation;

class SubscriptionPlanTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SubscriptionPlanTranslation::truncate();
        Schema::enableForeignKeyConstraints();

        $subscription_plan_translations = array(
            array('locale' => 'en', 'plan_id' => 1, 'name' => 'Super', 'description' => '1 month', 'note' => '', 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'plan_id' => 2, 'name' => 'Premium', 'description' => '6 months', 'note' => 'save ₹400', 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'plan_id' => 3, 'name' => 'Platinum', 'description' => '12 months', 'note' => 'save ₹900', 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'hi', 'plan_id' => 1, 'name' => 'बहुत अच्छा', 'description' => '1 महीना', 'note' => '', 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'hi', 'plan_id' => 2, 'name' => 'अधि मूल्य', 'description' => '6 महीने', 'note' => '₹400 बचाएं', 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'hi', 'plan_id' => 3, 'name' => 'प्लैटिनम', 'description' => '12 महीने', 'note' => '₹900 बचाएं', 'created_at' => now(), 'updated_at' => now()),
        );

        SubscriptionPlanTranslation::insert($subscription_plan_translations);
    }
}
