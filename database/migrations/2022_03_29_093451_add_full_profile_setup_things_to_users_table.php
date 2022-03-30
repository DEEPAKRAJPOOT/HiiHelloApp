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
            $table->string('my_voice')->nullable()->after('apple_id');
            $table->text('about_me')->nullable()->after('my_voice');

            //Profile Details
            $table->bigInteger('relationship_status')->unsigned()->nullable()->after('about_me');
            $table->bigInteger('you_are_here')->unsigned()->nullable()->after('relationship_status');
            $table->bigInteger('food_preference')->unsigned()->nullable()->after('you_are_here');
            $table->bigInteger('drinking')->unsigned()->nullable()->after('food_preference');
            $table->bigInteger('smoking')->unsigned()->nullable()->after('drinking');
            $table->bigInteger('star_sign')->unsigned()->nullable()->after('smoking');
            $table->bigInteger('fav_festival')->unsigned()->nullable()->after('star_sign');
            $table->bigInteger('religion')->unsigned()->nullable()->after('fav_festival');
            $table->bigInteger('community')->unsigned()->nullable()->after('religion');
            $table->bigInteger('pets')->unsigned()->nullable()->after('community');
            $table->bigInteger('education')->unsigned()->nullable()->after('pets');
            $table->bigInteger('occupation')->unsigned()->nullable()->after('education');

            $table->bigInteger('date_idea')->unsigned()->nullable()->after('occupation');
            $table->bigInteger('social_cause')->unsigned()->nullable()->after('date_idea');
            $table->bigInteger('risk_taken')->unsigned()->nullable()->after('social_cause');
            $table->bigInteger('perfect_relation')->unsigned()->nullable()->after('risk_taken');
            $table->bigInteger('my_mantra')->unsigned()->nullable()->after('perfect_relation');
            $table->bigInteger('one_thing_know')->unsigned()->nullable()->after('my_mantra');
            $table->bigInteger('worst_date')->unsigned()->nullable()->after('one_thing_know');
            $table->bigInteger('intro_family')->unsigned()->nullable()->after('worst_date');
            $table->bigInteger('found_one')->unsigned()->nullable()->after('intro_family');
            $table->bigInteger('about_surprising')->unsigned()->nullable()->after('found_one');
            $table->bigInteger('political_views')->unsigned()->nullable()->after('about_surprising');

            // Foreign Keys
            $table->foreign('relationship_status')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('you_are_here')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('food_preference')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('drinking')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('smoking')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('star_sign')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('fav_festival')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('religion')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('community')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pets')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('education')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('occupation')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('date_idea')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('social_cause')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('risk_taken')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('perfect_relation')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('my_mantra')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('one_thing_know')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('worst_date')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('intro_family')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('found_one')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('about_surprising')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('political_views')->references('id')->on('profile_details')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('users_relationship_status_foreign');
            $table->dropForeign('users_you_are_here_foreign');
            $table->dropForeign('users_food_preference_foreign');
            $table->dropForeign('users_drinking_foreign');
            $table->dropForeign('users_smoking_foreign');
            $table->dropForeign('users_star_sign_foreign');
            $table->dropForeign('users_fav_festival_foreign');
            $table->dropForeign('users_religion_foreign');
            $table->dropForeign('users_community_foreign');
            $table->dropForeign('users_pets_foreign');
            $table->dropForeign('users_education_foreign');
            $table->dropForeign('users_occupation_foreign');
            $table->dropForeign('users_date_idea_foreign');
            $table->dropForeign('users_social_cause_foreign');
            $table->dropForeign('users_risk_taken_foreign');
            $table->dropForeign('users_perfect_relation_foreign');
            $table->dropForeign('users_my_mantra_foreign');
            $table->dropForeign('users_one_thing_know_foreign');
            $table->dropForeign('users_worst_date_foreign');
            $table->dropForeign('users_intro_family_foreign');
            $table->dropForeign('users_found_one_foreign');
            $table->dropForeign('users_about_surprising_foreign');
            $table->dropForeign('users_political_views_foreign');

            $table->dropColumn(['my_voice','about_me','relationship_status','you_are_here','food_preference','drinking',
                'smoking','star_sign','fav_festival','religion','community','pets','education','occupation','date_idea',
                'social_cause','risk_taken','perfect_relation','my_mantra','one_thing_know','worst_date',
                'intro_family','found_one','about_surprising','political_views']);
        });
    }
}
