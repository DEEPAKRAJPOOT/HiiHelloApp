<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();

            $table->bigInteger('interest_id')->unsigned();
            $table->unique(['interest_id','locale']);
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade')->onUpdate('cascade');

            $table->string('title')->nullable();
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
        Schema::dropIfExists('interest_translations');
    }
}
