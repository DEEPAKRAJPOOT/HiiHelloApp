<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();
            $table->string('key')->nullable()->comment('FOR SPECIFIC TABLE COLUMN NAME');
            $table->string('value')->nullable()->comment('FOR SPECIFIC TABLE COLUMN VALUE');
            $table->string('user_id')->nullable()->comment('FOR REDIRECT TO USER DETAIL');
            $table->string('title')->nullable();
            $table->string('message')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
