<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();

            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('participate_id')->nullable();
            
            $table->enum('is_active', ['y', 'n'])->default('y')->nullable();
            
            $table->foreign('creator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('participate_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            
            $table->softDeletes();
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
        Schema::dropIfExists('chat_rooms');
    }
}
