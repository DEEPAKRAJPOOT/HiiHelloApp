<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->nullable();
            $table->string('slug')->nullable();
            $table->integer('edited_by')->unsigned()->nullable();
            $table->string('hint')->nullable();
            $table->string('file')->nullable();
            $table->enum('display_upload', ['y', 'n'])->default('y')->nullable();
            $table->timestamps();

            $table->foreign('edited_by')->references('id')->on('admins')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_pages');
    }
}
