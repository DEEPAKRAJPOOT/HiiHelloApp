<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SubscriptionPlan::truncate();
        Schema::enableForeignKeyConstraints();

        $subscription_plans = array(
            array(
                'custom_id'     =>  getUniqueString('subscription_plans'), 
                'months'        =>  1, 
                'amount'        =>  99, 
                'is_popular'    =>  'n', 
                'created_at'    =>  now(), 
                'updated_at'    =>  now(),
            ),
            array(
                'custom_id'     =>  getUniqueString('subscription_plans'), 
                'months'        =>  6, 
                'amount'        =>  199, 
                'is_popular'    =>  'y', 
                'created_at'    =>  now(), 
                'updated_at'    =>  now(),
            ),
            array(
                'custom_id'     =>  getUniqueString('subscription_plans'), 
                'months'        =>  12, 
                'amount'        =>  299, 
                'is_popular'    =>  'n', 
                'created_at'    =>  now(), 
                'updated_at'    =>  now(),
            ),
        );

        SubscriptionPlan::insert($subscription_plans);
    }
}
