<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslationFiledToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_translated']);
            $table->enum('is_trans_full_name', ['y', 'n'])->default('n')->nullable()->after('is_social_user');
            $table->enum('is_trans_about_me', ['y', 'n'])->default('n')->nullable()->after('is_trans_full_name');
            $table->enum('is_trans_fav_movie', ['y', 'n'])->default('n')->nullable()->after('is_trans_about_me');
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
            $table->enum('is_translated', ['y', 'n'])->default('n')->nullable()->after('is_social_user');
            $table->dropColumn(['is_trans_full_name', 'is_trans_about_me', 'is_trans_fav_movie']);
        });
    }
}
