<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_reports', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();

            $table->bigInteger('user_id')->unsigned()->nullable()->comment('reporter user');
            $table->bigInteger('reported_user_id')->unsigned()->nullable()->comment('reported user');

            $table->text('message')->nullable();
            $table->enum('status', ['Pending', 'Accepted', 'Rejected'])->default('Pending')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('profile_reports');
    }
}
