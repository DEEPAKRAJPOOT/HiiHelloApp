<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFavFestivalAndPetIdFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_fav_festival_id_foreign');
            $table->dropForeign('users_pet_id_foreign');
            $table->dropColumn(['fav_festival_id','pet_id']);
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
            $table->bigInteger('fav_festival_id')->unsigned()->nullable()->after('star_sign_id');
            $table->bigInteger('pet_id')->unsigned()->nullable()->after('community_id');

            $table->foreign('fav_festival_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pet_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
