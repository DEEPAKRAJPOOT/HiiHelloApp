<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name');

            $table->string('email')->unique();
            $table->string('contact_no')->nullable();
            $table->string('password');

            $table->text('permissions')->nullable();

            $table->enum('is_active', ['y', 'n'])->default('y')->nullable();
            $table->enum('type', ['admin', 'role'])->default('role')->nullable();

            $table->string('profile')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
