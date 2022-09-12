<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dis_likes', function (Blueprint $table) {
            $table->id();

            $table->string('custom_id')->nullable();

            $table->bigInteger('user_id')->unsigned()->nullable()->comment('user details of which we have dislike');
            $table->bigInteger('dis_liker_id')->unsigned()->nullable()->comment('login user details who dislike other profile');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dis_liker_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('dis_likes');
    }
}
