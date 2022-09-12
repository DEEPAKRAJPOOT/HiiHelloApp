<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterParentIdToInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interests', function (Blueprint $table) {
            $table->bigInteger('master_parent_id')->unsigned()->nullable()->after('parent_id');
            $table->foreign('master_parent_id')->references('id')->on('interests')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('interests_master_parent_id_foreign');
            $table->dropColumn(['master_parent_id']);
        });
    }
}
