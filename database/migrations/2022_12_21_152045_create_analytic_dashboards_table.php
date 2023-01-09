<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticDashboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytic_dashboards', function (Blueprint $table) {
            $table->id();
            $table->integer('total_users')->nullable()->default(0);
            $table->integer('per_day_users')->nullable()->default(0);
            $table->integer('per_week_users')->nullable()->default(0);
            $table->integer('per_30_day_users')->nullable()->default(0);
            $table->integer('male_users')->nullable()->default(0);
            $table->integer('female_users')->nullable()->default(0);
            $table->integer('na_users')->nullable()->default(0);
            $table->integer('male_18_25')->nullable()->default(0);
            $table->integer('male_26_35')->nullable()->default(0);
            $table->integer('male_36_45')->nullable()->default(0);
            $table->integer('male_45')->nullable()->default(0);
            $table->integer('female_18_25')->nullable()->default(0);
            $table->integer('female_26_35')->nullable()->default(0);
            $table->integer('female_36_45')->nullable()->default(0);
            $table->integer('female_45')->nullable()->default(0);
            $table->integer('paid_users')->nullable()->default(0);
            $table->integer('non_paid_users')->nullable()->default(0);
            $table->integer('total_phone_users')->nullable()->default(0);
            $table->integer('total_google_users')->nullable()->default(0);
            $table->integer('total_facebook_users')->nullable()->default(0);
            $table->integer('total_apple_users')->nullable()->default(0);
            $table->integer('male_phone_verified')->nullable()->default(0);
            $table->integer('male_phone_unverified')->nullable()->default(0);
            $table->integer('male_email_verified')->nullable()->default(0);
            $table->integer('male_email_unverified')->nullable()->default(0);
            $table->integer('male_photo_verified')->nullable()->default(0);
            $table->integer('male_photo_unverified')->nullable()->default(0);
            $table->integer('male_account_verified')->nullable()->default(0);
            $table->integer('male_account_unverified')->nullable()->default(0);
            $table->integer('female_phone_verified')->nullable()->default(0);
            $table->integer('female_phone_unverified')->nullable()->default(0);
            $table->integer('female_email_verified')->nullable()->default(0);
            $table->integer('female_email_unverified')->nullable()->default(0);
            $table->integer('female_photo_verified')->nullable()->default(0);
            $table->integer('female_photo_unverified')->nullable()->default(0);
            $table->integer('female_account_verified')->nullable()->default(0);
            $table->integer('female_account_unverified')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analytic_dashboards');
    }
}
