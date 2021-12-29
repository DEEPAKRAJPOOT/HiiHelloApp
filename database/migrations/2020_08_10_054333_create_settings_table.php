<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type')->nullable();
            $table->string('constant')->nullable();
            $table->string('options')->nullable();
            $table->string('class')->nullable();
            $table->string('icon')->nullable();
            $table->enum('required', ['y', 'n'])->default('y');
            $table->text('value')->nullable();
            $table->string('hint')->nullable();
            $table->enum('editable', ['y', 'n'])->default('n');
            $table->timestamps();
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
        Schema::dropIfExists('settings');
    }
}
