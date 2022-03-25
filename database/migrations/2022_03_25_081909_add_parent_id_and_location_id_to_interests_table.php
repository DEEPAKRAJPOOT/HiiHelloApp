<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdAndLocationIdToInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interests', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned()->nullable()->after('custom_id');
            $table->bigInteger('location_id')->unsigned()->nullable()->after('parent_id');

            $table->foreign('parent_id')->references('id')->on('interests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interests', function (Blueprint $table) {
            $table->dropForeign('interests_parent_id_foreign');
            $table->dropForeign('interests_location_id_foreign');
            $table->dropColumn(['parent_id','location_id']);
        });
    }
}
