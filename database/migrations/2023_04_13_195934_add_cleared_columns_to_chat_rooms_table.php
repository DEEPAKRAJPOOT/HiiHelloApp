<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClearedColumnsToChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->timestamp('creator_cleared_at')->nullable()->after('is_active');
            $table->timestamp('participate_cleared_at')->nullable()->after('creator_cleared_at');
            $table->timestamp('creator_deleted_at')->nullable()->after('participate_cleared_at');
            $table->timestamp('participate_deleted_at')->nullable()->after('creator_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropColumn(['creator_cleared_at','participate_cleared_at','creator_deleted_at','participate_deleted_at']);
        });
    }
}
