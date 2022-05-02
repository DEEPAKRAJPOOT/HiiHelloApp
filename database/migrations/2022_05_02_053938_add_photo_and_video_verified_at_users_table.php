<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoAndVideoVerifiedAtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verify_photo')->nullable()->after('profile_photo');
            $table->string('verify_video')->nullable()->after('verify_photo');
            $table->enum('verify_status', ['under_review', 'verified', 'unverified'])->default('under_review')->nullable()->after('verify_video');

            $table->timestamp('photo_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('video_verified_at')->nullable()->after('photo_verified_at');
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
            $table->dropColumn(['verify_photo', 'verify_video', 'verify_status', 'photo_verified_at','video_verified_at']);
        });
    }
}
