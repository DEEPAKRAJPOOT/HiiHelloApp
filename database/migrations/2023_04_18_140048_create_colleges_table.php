<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();
            $table->string('name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('university')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colleges');
    }
}
