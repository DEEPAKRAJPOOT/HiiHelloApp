<?php

namespace Database\Seeders;

use App\Models\ProfileDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ProfileDetail::truncate();
        Schema::enableForeignKeyConstraints();

        $profile_detail = array(
            array('slug' => "relationship-status-single", 'attribute' => "relationship_status", 'type' => "string"),
            array('slug' => "relationship-status-single-with-kids", 'attribute' => "relationship_status", 'type' => "string"),
            array('slug' => "relationship-status-divorced", 'attribute' => "relationship_status", 'type' => "string"),
            array('slug' => "relationship-status-divorced-with-kids", 'attribute' => "relationship_status", 'type' => "string"),
            array('slug' => "relationship-status-widowed", 'attribute' => "relationship_status", 'type' => "string"),
            array('slug' => "relationship-status-widowed-with-kids", 'attribute' => "relationship_status", 'type' => "string"),
            array('slug' => "relationship-status-separated-with-kids", 'attribute' => "relationship_status", 'type' => "string"),

            array('slug' => "you-are-here-relationship", 'attribute' => "you_are_here", 'type' => "string"),
            array('slug' => "you-are-here-casual", 'attribute' => "you_are_here", 'type' => "string"),
            array('slug' => "you-are-here-both", 'attribute' => "you_are_here", 'type' => "string"),
            array('slug' => "you-are-here-dont-know", 'attribute' => "you_are_here", 'type' => "string"),

            array('slug' => "food-preference-veg", 'attribute' => "food_preference", 'type' => "string"),
            array('slug' => "food-preference-non-veg", 'attribute' => "food_preference", 'type' => "string"),
            array('slug' => "food-preference-vegan", 'attribute' => "food_preference", 'type' => "string"),
            array('slug' => "food-preference-eggetarian", 'attribute' => "food_preference", 'type' => "string"),

            array('slug' => "drinking-frequently", 'attribute' => "drinking", 'type' => "string"),
            array('slug' => "drinking-socially", 'attribute' => "drinking", 'type' => "string"),
            array('slug' => "drinking-never", 'attribute' => "drinking", 'type' => "string"),

            array('slug' => "smoking-frequently", 'attribute' => "smoking", 'type' => "string"),
            array('slug' => "smoking-socially", 'attribute' => "smoking", 'type' => "string"),
            array('slug' => "smoking-never", 'attribute' => "smoking", 'type' => "string"),

            array('slug' => "star-sign-aries", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-taurus", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-gemini", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-cancer", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-leo", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-virgo", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-libra", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-scorpio", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-sagittarius", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-capricorn", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-aquarius", 'attribute' => "star_sign", 'type' => "string"),
            array('slug' => "star-sign-pisces", 'attribute' => "star_sign", 'type' => "string"),

            array('slug' => "fav-festival-diwali", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-holi", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-christmas", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-dussehra", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-durga-puja", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-ganesh-chaturthi", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-eid-ul-fitr", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-onam", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-raksha-bandhan", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-pongal", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-gurupurab", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-maha-shivratri", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-lodi", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-buddha-purnima", 'attribute' => "fav_festival", 'type' => "string"),
            array('slug' => "fav-festival-makar-sankranti", 'attribute' => "fav_festival", 'type' => "string"),

            array('slug' => "religion-hindu", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-christian", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-muslim", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-sikh", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-jain", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-parsi", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-buddhist", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-jewish", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-spiritual", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-atheist", 'attribute' => "religion", 'type' => "string"),
            array('slug' => "religion-agnostic", 'attribute' => "religion", 'type' => "string"),

            array('slug' => "pets-dogs", 'attribute' => "pets", 'type' => "string"),
            array('slug' => "pets-cats", 'attribute' => "pets", 'type' => "string"),
            array('slug' => "pets-other", 'attribute' => "pets", 'type' => "string"),
            array('slug' => "pets-dont-want", 'attribute' => "pets", 'type' => "string"),
            array('slug' => "pets-undecided", 'attribute' => "pets", 'type' => "string"),

            array('slug' => "education-finished-schooling", 'attribute' => "education", 'type' => "string"),
            array('slug' => "education-under-graduate", 'attribute' => "education", 'type' => "string"),
            array('slug' => "education-graduate", 'attribute' => "education", 'type' => "string"),
            array('slug' => "education-post-graduate", 'attribute' => "education", 'type' => "string"),
            array('slug' => "education-phd", 'attribute' => "education", 'type' => "string"),
            array('slug' => "education-other", 'attribute' => "education", 'type' => "string"),

            array('slug' => "occupation-artist", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-business-analyst", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-designer", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-entrepreneur", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-freelancer", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-social-worker", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-doctor", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-nurse", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-teacher", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-blogger", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-vlogger", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-engineer", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-business", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-animator", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-film-maker", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-defense", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-writer", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-sports", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-farmer", 'attribute' => "occupation", 'type' => "string"),
            array('slug' => "occupation-other", 'attribute' => "occupation", 'type' => "string"),

            array('slug' => "date-idea-take-a-long-walk-in-the-park", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-meet-for-breakfast", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-browse-a-bookstore", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-take-a-cooking-class", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-visit-an-outdoor-marke", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-plan-a-day-trip", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-go-for-a-picnic", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-take-a-painting-class", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-watch-a-movie", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-visit-an-museum", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-take-a-city-tour", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-take-an-adventurous-tour", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-visit-a-popular-restaurant", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-go-party-hopping", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-going-to-an-amusement-park", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-a-trip-to-an-ice-cream-store", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-a-day-at-the-zoo", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-playing-videogames-together", 'attribute' => "date_idea", 'type' => "string"),
            array('slug' => "date-idea-listening-to-music-together", 'attribute' => "date_idea", 'type' => "string"),

            array('slug' => "social-cause-educating-underprivileged-children-and-helping-them-grow", 'attribute' => "social_cause", 'type' => "string"),

            array('slug' => "risk-taken-not-wearing-a-jacket-in-minus-10-degrees!", 'attribute' => "risk_taken", 'type' => "string"),

            array('slug' => "perfect-relation-trust-laughter-and-understanding", 'attribute' => "perfect_relation_things", 'type' => "string"),

            array('slug' => "my-mantra-live-and-let-live", 'attribute' => "my_mantra", 'type' => "string"),

            array('slug' => "thing-about-me-i-love-jagjit-singh-so-be-ready-for-a-ghazal-session-on-our-dates!", 'attribute' => "thing_about_me", 'type' => "string"),

            array('slug' => "worst-date-when-i-forgot-my-wallet-at-home", 'attribute' => "worst_date", 'type' => "string"),

            array('slug' => "introduce-to-family-you-can-rock-a-traditional-outfit", 'attribute' => "introduce_to_family", 'type' => "string"),

            array('slug' => "found-the-one-they-can-make-me-laugh-when-i-want-to-cry!", 'attribute' => "found_the_one", 'type' => "string"),

            array('slug' => "about-me-surprises-i-have-never-had-a-pizza-in-my-life", 'attribute' => "about_me_surprises", 'type' => "string"),

            array('slug' => "political-views-all-political-parties-divide-citizens-we-need-presidents-rule-in-the-country", 'attribute' => "political_views", 'type' => "string"),
        );

        ProfileDetail::insert($profile_detail);
    }
}
