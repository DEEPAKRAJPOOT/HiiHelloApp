<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollegeIdAndIsTestUserToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('college_id')->nullable()->after('university_id');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('is_test_user',['y','n'])->default('n')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['college_id','is_test_user']);
        });
    }
}
