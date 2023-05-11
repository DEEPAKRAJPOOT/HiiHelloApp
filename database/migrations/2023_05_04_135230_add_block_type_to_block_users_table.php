<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockTypeToBlockUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('block_users', function (Blueprint $table) {
            $table->enum('block_type',['block', 'hide'])->default('block')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('block_users', function (Blueprint $table) {
            $table->dropColumn(['block_type']);
        });
    }
}
