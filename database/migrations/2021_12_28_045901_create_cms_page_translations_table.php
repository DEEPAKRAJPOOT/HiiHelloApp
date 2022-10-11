<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsPageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_page_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();

            $table->bigInteger('cms_page_id')->unsigned();
            $table->unique(['cms_page_id','locale']);
            $table->foreign('cms_page_id')->references('id')->on('cms_pages')->onDelete('cascade')->onUpdate('cascade');

            $table->string('title')->nullable();
            $table->text('description')->nullable();

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
        Schema::dropIfExists('cms_page_translations');
    }
}
