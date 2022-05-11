<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropExtraColumnsFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_occupation_id_foreign');
            $table->dropForeign('users_date_idea_id_foreign');
            $table->dropForeign('users_social_cause_id_foreign');
            $table->dropForeign('users_risk_taken_id_foreign');
            $table->dropForeign('users_perfect_relation_id_foreign');
            $table->dropForeign('users_my_mantra_id_foreign');
            $table->dropForeign('users_one_thing_know_id_foreign');
            $table->dropForeign('users_worst_date_id_foreign');
            $table->dropForeign('users_intro_family_id_foreign');
            $table->dropForeign('users_found_one_id_foreign');
            $table->dropForeign('users_about_surprising_id_foreign');
            $table->dropForeign('users_political_view_id_foreign');

            $table->dropColumn(['occupation_id','date_idea_id',
                'social_cause_id','risk_taken_id','perfect_relation_id','my_mantra_id','one_thing_know_id','worst_date_id',
                'intro_family_id','found_one_id','about_surprising_id','political_view_id']);
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
            $table->bigInteger('occupation_id')->unsigned()->nullable()->after('education_id');
            $table->bigInteger('date_idea_id')->unsigned()->nullable()->after('occupation_id');
            $table->bigInteger('social_cause_id')->unsigned()->nullable()->after('date_idea_id');
            $table->bigInteger('risk_taken_id')->unsigned()->nullable()->after('social_cause_id');
            $table->bigInteger('perfect_relation_id')->unsigned()->nullable()->after('risk_taken_id');
            $table->bigInteger('my_mantra_id')->unsigned()->nullable()->after('perfect_relation_id');
            $table->bigInteger('one_thing_know_id')->unsigned()->nullable()->after('my_mantra_id');
            $table->bigInteger('worst_date_id')->unsigned()->nullable()->after('one_thing_know_id');
            $table->bigInteger('intro_family_id')->unsigned()->nullable()->after('worst_date_id');
            $table->bigInteger('found_one_id')->unsigned()->nullable()->after('intro_family_id');
            $table->bigInteger('about_surprising_id')->unsigned()->nullable()->after('found_one_id');
            $table->bigInteger('political_view_id')->unsigned()->nullable()->after('about_surprising_id');

            $table->foreign('occupation_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('date_idea_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('social_cause_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('risk_taken_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('perfect_relation_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('my_mantra_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('one_thing_know_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('worst_date_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('intro_family_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('found_one_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('about_surprising_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('political_view_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
