<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passion_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();

            $table->bigInteger('passion_id')->unsigned();
            $table->unique(['passion_id','locale']);
            $table->foreign('passion_id')->references('id')->on('passions')->onDelete('cascade')->onUpdate('cascade');

            $table->string('name')->nullable();
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
        Schema::dropIfExists('passion_translations');
    }
}
