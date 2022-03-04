<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->text('token')->nullable();
            $table->enum('type', ['android', 'ios'])->nullable();

            /* Device Info */
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->string('os_name')->nullable()->comment('OS NAME');
            $table->string('os_version')->nullable()->comment('NUMERIC VERSION');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('device_tokens');
    }
}
