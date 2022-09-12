<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscoverThingsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('discover_distance')->nullable()->after('education_id');
            $table->integer('discover_start_age')->nullable()->after('discover_distance');
            $table->integer('discover_end_age')->nullable()->after('discover_start_age');

            $table->bigInteger('discover_location_id')->unsigned()->nullable()->after('discover_end_age');
            $table->foreign('discover_location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_discover_location_id_foreign');
            $table->dropColumn(['discover_distance', 'discover_start_age', 'discover_end_age', 'discover_location_id']);
        });
    }
}
