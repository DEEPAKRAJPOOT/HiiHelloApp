<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationDetailsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('verify_email_send', ['y', 'n'])->default('n')->nullable()->after('political_view_id');

            $table->text('photo_suggestion')->nullable()->after('verify_video');
            $table->text('video_suggestion')->nullable()->after('photo_suggestion');

            $table->enum('verify_photo_status', ['pending', 'under_review', 'verified', 'unverified'])->default('pending')->nullable()->after('video_suggestion');
            $table->enum('verify_video_status', ['pending', 'under_review', 'verified', 'unverified'])->default('pending')->nullable()->after('verify_photo_status');
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
            $table->dropColumn(['verify_email_send', 'photo_suggestion', 'video_suggestion', 'verify_photo_status','verify_video_status']);
        });
    }
}
