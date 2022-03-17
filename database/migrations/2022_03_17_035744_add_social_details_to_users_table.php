<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialDetailsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('is_social_user', ['y', 'n'])->default('n')->nullable()->after('is_active');
            $table->text('facebook_id')->nullable()->after('is_social_user');
            $table->text('google_id')->nullable()->after('facebook_id');
            $table->text('apple_id')->nullable()->after('google_id');
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
            $table->dropColumn(['is_social_user','facebook_id','google_id','apple_id']);
        });
    }
}
