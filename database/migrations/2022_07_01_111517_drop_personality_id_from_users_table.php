<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPersonalityIdFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_personality_id_foreign');
            $table->dropColumn('personality_id');
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
            $table->bigInteger('personality_id')->unsigned()->nullable()->after('apple_id');
            $table->foreign('personality_id')->references('id')->on('personalities')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
