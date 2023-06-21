<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVanishModeToChatRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->enum('vanish_mode', ['n', 'y'])->nullable()->default('n')->after('is_active');
            $table->unsignedBigInteger('vanish_mode_by')->nullable()->after('vanish_mode');
            $table->enum('disappear_mode', ['off', '1h', '24h', '7d'])->nullable()->default('off')->after('vanish_mode_by');
            $table->unsignedBigInteger('disappear_mode_by')->nullable()->after('disappear_mode');

            $table->foreign('vanish_mode_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('disappear_mode_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropColumn(['vanish_mode','vanish_mode_by','disappear_mode','disappear_mode_by']);
        });
    }
}
