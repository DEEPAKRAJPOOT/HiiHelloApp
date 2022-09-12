<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslationFlagsToPersonalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personalities', function (Blueprint $table) {
            $table->enum('is_trans_title', ['y', 'n'])->default('n')->nullable()->after('image');
            $table->enum('is_trans_description', ['y', 'n'])->default('n')->nullable()->after('is_trans_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personalities', function (Blueprint $table) {
            $table->dropColumn(['is_trans_title', 'is_trans_description']);
        });
    }
}
