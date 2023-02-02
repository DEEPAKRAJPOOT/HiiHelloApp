<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalOtpLessUsersToAnalyticDasboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analytic_dashboard', function (Blueprint $table) {
            $table->bigInteger('total_otp_less_users')->nullable()->default(0)->after('total_apple_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analytic_dashboard', function (Blueprint $table) {
            $table->dropColumn(['total_otp_less_users']);
        });
    }
}
