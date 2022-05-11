<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullProfileDetailsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            //Profile Details
            $table->bigInteger('personality_id')->unsigned()->nullable()->after('about_me');
            $table->bigInteger('university_id')->unsigned()->nullable()->after('personality_id');
            $table->bigInteger('profession_id')->unsigned()->nullable()->after('university_id');

            // Foreign Keys
            $table->foreign('personality_id')->references('id')->on('personalities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('university_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('profession_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('users_personality_id_foreign');
            $table->dropForeign('users_university_id_foreign');
            $table->dropForeign('users_profession_id_foreign');

            $table->dropColumn(['personality_id', 'university_id', 'profession_id']);
        });
    }
}
