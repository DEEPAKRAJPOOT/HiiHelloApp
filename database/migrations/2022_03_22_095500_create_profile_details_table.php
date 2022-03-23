<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_details', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->string('attribute')->nullable()->comment('attribute name like food,pets,educaction');
            $table->string('type')->nullable()->comment('STRING, NUM');
            $table->enum('is_active', ['y', 'n'])->default('y')->nullable();

            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_details');
    }
}
