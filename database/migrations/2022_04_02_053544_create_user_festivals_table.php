<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFestivalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_festivals', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('festival_id')->unsigned()->nullable()->comment('connected with profile_details table');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('festival_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('user_festivals');
    }
}
