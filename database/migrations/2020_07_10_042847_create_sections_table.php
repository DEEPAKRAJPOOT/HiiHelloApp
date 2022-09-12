<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable();
            
            $table->string('icon')->nullable()->comment('font awesome, simple line or glyphicons icons');
            $table->string('image')->nullable()->comment('for image file');
            $table
                ->enum('icon_type', ['image', 'font-awesome', 'line-icons', 'glyphicons', 'other'])
                ->default('font-awesome')
                ->nullable()->comment('icon type');

            $table->integer('sequence')->default(1)->nullable();
            $table->enum('is_active', ['y', 'n'])->default('y')->nullable();

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
        Schema::dropIfExists('sections');
    }
}
