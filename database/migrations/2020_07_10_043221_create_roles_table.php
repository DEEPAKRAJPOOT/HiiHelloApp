<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('section_id')->unsigned();
            $table->string('title')->nullable();

            $table->string('route')->nullable();
            $table->string('params')->nullable()->comment('pass extra parameters as a slug');

            $table->string('icon')->nullable()->comment('font awesome, simple line or glyphicons icons');
            $table->string('image')->nullable()->comment('for image file');
            $table
                ->enum('icon_type', ['image', 'font-awesome', 'line-icons', 'glyphicons', 'other'])
                ->default('font-awesome')
                ->nullable()->comment('icon type');

            $table->string('allowed_permissions')->nullable();

            $table->integer('sequence')->default(1)->nullable();

            $table->enum('is_display',['y','n'])->default('y')->nullable();
            $table->enum('is_active', ['y', 'n'])->default('y')->nullable();

            $table->timestamps();
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
