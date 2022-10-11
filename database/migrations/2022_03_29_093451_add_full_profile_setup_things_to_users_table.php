<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFullProfileSetupThingsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('about_me')->nullable()->after('apple_id');

            //Profile Details
            $table->bigInteger('relationship_status_id')->unsigned()->nullable()->after('about_me');
            $table->bigInteger('you_are_here_id')->unsigned()->nullable()->after('relationship_status_id');
            $table->bigInteger('food_preference_id')->unsigned()->nullable()->after('you_are_here_id');
            $table->bigInteger('drinking_id')->unsigned()->nullable()->after('food_preference_id');
            $table->bigInteger('smoking_id')->unsigned()->nullable()->after('drinking_id');
            $table->bigInteger('star_sign_id')->unsigned()->nullable()->after('smoking_id');
            $table->bigInteger('fav_festival_id')->unsigned()->nullable()->after('star_sign_id');
            $table->bigInteger('religion_id')->unsigned()->nullable()->after('fav_festival_id');
            $table->bigInteger('community_id')->unsigned()->nullable()->after('religion_id');
            $table->bigInteger('pet_id')->unsigned()->nullable()->after('community_id');
            $table->bigInteger('education_id')->unsigned()->nullable()->after('pet_id');
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

            // Foreign Keys
            $table->foreign('relationship_status_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('you_are_here_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('food_preference_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('drinking_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('smoking_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('star_sign_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fav_festival_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('religion_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('community_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pet_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('education_id')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_relationship_status_id_foreign');
            $table->dropForeign('users_you_are_here_id_foreign');
            $table->dropForeign('users_food_preference_id_foreign');
            $table->dropForeign('users_drinking_id_foreign');
            $table->dropForeign('users_smoking_id_foreign');
            $table->dropForeign('users_star_sign_id_foreign');
            $table->dropForeign('users_fav_festival_id_foreign');
            $table->dropForeign('users_religion_id_foreign');
            $table->dropForeign('users_community_id_foreign');
            $table->dropForeign('users_pet_id_foreign');
            $table->dropForeign('users_education_id_foreign');
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

            $table->dropColumn(['about_me','relationship_status_id','you_are_here_id','food_preference_id','drinking_id',
                'smoking_id','star_sign_id','fav_festival_id','religion_id','community_id','pet_id','education_id','occupation_id','date_idea_id',
                'social_cause_id','risk_taken_id','perfect_relation_id','my_mantra_id','one_thing_know_id','worst_date_id',
                'intro_family_id','found_one_id','about_surprising_id','political_view_id']);
        });
    }
}
