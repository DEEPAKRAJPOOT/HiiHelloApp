<?php

namespace Database\Seeders;

use App\Models\ProfileDetailTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileDetailTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ProfileDetailTranslation::truncate();
        Schema::enableForeignKeyConstraints();

        $profile_detail_trans = array(
            array('locale' => 'en', 'profile_detail_id' => 1, 'value' => "Single"),
            array('locale' => 'en', 'profile_detail_id' => 2, 'value' => "Single with kids"),
            array('locale' => 'en', 'profile_detail_id' => 3, 'value' => "Divorced"),
            array('locale' => 'en', 'profile_detail_id' => 4, 'value' => "Divorced with kids"),
            array('locale' => 'en', 'profile_detail_id' => 5, 'value' => "Widowed"),
            array('locale' => 'en', 'profile_detail_id' => 6, 'value' => "Widowed with kids"),
            array('locale' => 'en', 'profile_detail_id' => 7, 'value' => "Separated with kids"),

            array('locale' => 'en', 'profile_detail_id' => 8, 'value' => "Relationship"),
            array('locale' => 'en', 'profile_detail_id' => 9, 'value' => "Casual"),
            array('locale' => 'en', 'profile_detail_id' => 10, 'value' => "Both"),
            array('locale' => 'en', 'profile_detail_id' => 11, 'value' => "Don't Know"),

            array('locale' => 'en', 'profile_detail_id' => 12, 'value' => "Veg"),
            array('locale' => 'en', 'profile_detail_id' => 13, 'value' => "Non-Veg"),
            array('locale' => 'en', 'profile_detail_id' => 14, 'value' => "Vegan"),
            array('locale' => 'en', 'profile_detail_id' => 15, 'value' => "Eggetarian"),

            array('locale' => 'en', 'profile_detail_id' => 16, 'value' => "Frequently"),
            array('locale' => 'en', 'profile_detail_id' => 17, 'value' => "Socially"),
            array('locale' => 'en', 'profile_detail_id' => 18, 'value' => "Never"),

            array('locale' => 'en', 'profile_detail_id' => 19, 'value' => "Frequently"),
            array('locale' => 'en', 'profile_detail_id' => 20, 'value' => "Socially"),
            array('locale' => 'en', 'profile_detail_id' => 21, 'value' => "Never"),

            array('locale' => 'en', 'profile_detail_id' => 22, 'value' => "Aries"),
            array('locale' => 'en', 'profile_detail_id' => 23, 'value' => "Taurus"),
            array('locale' => 'en', 'profile_detail_id' => 24, 'value' => "Gemini"),
            array('locale' => 'en', 'profile_detail_id' => 25, 'value' => "Cancer"),
            array('locale' => 'en', 'profile_detail_id' => 26, 'value' => "Leo"),
            array('locale' => 'en', 'profile_detail_id' => 27, 'value' => "Virgo"),
            array('locale' => 'en', 'profile_detail_id' => 28, 'value' => "Libra"),
            array('locale' => 'en', 'profile_detail_id' => 29, 'value' => "Scorpio"),
            array('locale' => 'en', 'profile_detail_id' => 30, 'value' => "Sagittarius"),
            array('locale' => 'en', 'profile_detail_id' => 31, 'value' => "Capricorn"),
            array('locale' => 'en', 'profile_detail_id' => 32, 'value' => "Aquarius"),
            array('locale' => 'en', 'profile_detail_id' => 33, 'value' => "Pisces"),

            array('locale' => 'en', 'profile_detail_id' => 34, 'value' => "Diwali"),
            array('locale' => 'en', 'profile_detail_id' => 35, 'value' => "Holi"),
            array('locale' => 'en', 'profile_detail_id' => 36, 'value' => "Christmas"),
            array('locale' => 'en', 'profile_detail_id' => 37, 'value' => "Dussehra"),
            array('locale' => 'en', 'profile_detail_id' => 38, 'value' => "Durga Puja"),
            array('locale' => 'en', 'profile_detail_id' => 39, 'value' => "Ganesh Chaturthi"),
            array('locale' => 'en', 'profile_detail_id' => 40, 'value' => "Eid-Ul-Fitr"),
            array('locale' => 'en', 'profile_detail_id' => 41, 'value' => "Onam"),
            array('locale' => 'en', 'profile_detail_id' => 42, 'value' => "Raksha Bandhan"),
            array('locale' => 'en', 'profile_detail_id' => 43, 'value' => "Pongal"),
            array('locale' => 'en', 'profile_detail_id' => 44, 'value' => "Gurupurab"),
            array('locale' => 'en', 'profile_detail_id' => 45, 'value' => "Maha Shivratri"),
            array('locale' => 'en', 'profile_detail_id' => 46, 'value' => "Lodi"),
            array('locale' => 'en', 'profile_detail_id' => 47, 'value' => "Buddha Purnima"),
            array('locale' => 'en', 'profile_detail_id' => 48, 'value' => "Makar Sankranti"),

            array('locale' => 'en', 'profile_detail_id' => 49, 'value' => "Hindu"),
            array('locale' => 'en', 'profile_detail_id' => 50, 'value' => "Christian"),
            array('locale' => 'en', 'profile_detail_id' => 51, 'value' => "Muslim"),
            array('locale' => 'en', 'profile_detail_id' => 52, 'value' => "Sikh"),
            array('locale' => 'en', 'profile_detail_id' => 53, 'value' => "Jain"),
            array('locale' => 'en', 'profile_detail_id' => 54, 'value' => "Parsi"),
            array('locale' => 'en', 'profile_detail_id' => 55, 'value' => "Buddhist"),
            array('locale' => 'en', 'profile_detail_id' => 56, 'value' => "Jewish"),
            array('locale' => 'en', 'profile_detail_id' => 57, 'value' => "Spiritual"),
            array('locale' => 'en', 'profile_detail_id' => 58, 'value' => "Atheist"),
            array('locale' => 'en', 'profile_detail_id' => 59, 'value' => "Agnostic"),

            array('locale' => 'en', 'profile_detail_id' => 60, 'value' => "Dogs"),
            array('locale' => 'en', 'profile_detail_id' => 61, 'value' => "Cats"),
            array('locale' => 'en', 'profile_detail_id' => 62, 'value' => "Other"),
            array('locale' => 'en', 'profile_detail_id' => 63, 'value' => "Don't Want"),
            array('locale' => 'en', 'profile_detail_id' => 64, 'value' => "Undecided"),

            array('locale' => 'en', 'profile_detail_id' => 65, 'value' => "Finished Schooling"),
            array('locale' => 'en', 'profile_detail_id' => 66, 'value' => "Under Graduate"),
            array('locale' => 'en', 'profile_detail_id' => 67, 'value' => "Graduate"),
            array('locale' => 'en', 'profile_detail_id' => 68, 'value' => "Post-Graduate"),
            array('locale' => 'en', 'profile_detail_id' => 69, 'value' => "Ph.D"),
            array('locale' => 'en', 'profile_detail_id' => 70, 'value' => "Other"),
            
            array('locale' => 'en', 'profile_detail_id' => 71, 'value' => "Artist"),
            array('locale' => 'en', 'profile_detail_id' => 72, 'value' => "Business Analyst"),
            array('locale' => 'en', 'profile_detail_id' => 73, 'value' => "Designer"),
            array('locale' => 'en', 'profile_detail_id' => 74, 'value' => "Entrepreneur"),
            array('locale' => 'en', 'profile_detail_id' => 75, 'value' => "Freelancer"),
            array('locale' => 'en', 'profile_detail_id' => 76, 'value' => "Social Worker"),
            array('locale' => 'en', 'profile_detail_id' => 77, 'value' => "Doctor"),
            array('locale' => 'en', 'profile_detail_id' => 78, 'value' => "Nurse"),
            array('locale' => 'en', 'profile_detail_id' => 79, 'value' => "Teacher"),
            array('locale' => 'en', 'profile_detail_id' => 80, 'value' => "Blogger"),
            array('locale' => 'en', 'profile_detail_id' => 81, 'value' => "Vlogger"),
            array('locale' => 'en', 'profile_detail_id' => 82, 'value' => "Engineer"),
            array('locale' => 'en', 'profile_detail_id' => 83, 'value' => "Business"),
            array('locale' => 'en', 'profile_detail_id' => 84, 'value' => "Animator"),
            array('locale' => 'en', 'profile_detail_id' => 85, 'value' => "Film Maker"),
            array('locale' => 'en', 'profile_detail_id' => 86, 'value' => "Defense"),
            array('locale' => 'en', 'profile_detail_id' => 87, 'value' => "Writer"),
            array('locale' => 'en', 'profile_detail_id' => 88, 'value' => "Sports"),
            array('locale' => 'en', 'profile_detail_id' => 89, 'value' => "Farmer"),
            array('locale' => 'en', 'profile_detail_id' => 90, 'value' => "Other"),

            array('locale' => 'en', 'profile_detail_id' => 91, 'value' => "Take a long walk in the park"),
            array('locale' => 'en', 'profile_detail_id' => 92, 'value' => "Meet for breakfast"),
            array('locale' => 'en', 'profile_detail_id' => 93, 'value' => "Browse a bookstore"),
            array('locale' => 'en', 'profile_detail_id' => 94, 'value' => "Take a cooking class"),
            array('locale' => 'en', 'profile_detail_id' => 95, 'value' => "Visit an outdoor marke"),
            array('locale' => 'en', 'profile_detail_id' => 96, 'value' => "Plan a day trip"),
            array('locale' => 'en', 'profile_detail_id' => 97, 'value' => "Go for a picnic"),
            array('locale' => 'en', 'profile_detail_id' => 98, 'value' => "Take a painting class"),
            array('locale' => 'en', 'profile_detail_id' => 99, 'value' => "Watch a movie"),
            array('locale' => 'en', 'profile_detail_id' => 100, 'value' => "Visit an museum"),
            array('locale' => 'en', 'profile_detail_id' => 101, 'value' => "Take a city tour"),
            array('locale' => 'en', 'profile_detail_id' => 102, 'value' => "Take an adventurous tour"),
            array('locale' => 'en', 'profile_detail_id' => 103, 'value' => "Visit a popular restaurant"),
            array('locale' => 'en', 'profile_detail_id' => 104, 'value' => "Go party hopping"),
            array('locale' => 'en', 'profile_detail_id' => 105, 'value' => "Going to an amusement park"),
            array('locale' => 'en', 'profile_detail_id' => 106, 'value' => "A trip to an ice cream store"),
            array('locale' => 'en', 'profile_detail_id' => 107, 'value' => "A day at the zoo"),
            array('locale' => 'en', 'profile_detail_id' => 108, 'value' => "Playing videogames together"),
            array('locale' => 'en', 'profile_detail_id' => 109, 'value' => "Listening to music together"),

            array('locale' => 'en', 'profile_detail_id' => 110, 'value' => "Educating underprivileged children and helping them grow"),

            array('locale' => 'en', 'profile_detail_id' => 111, 'value' => "Not wearing a jacket in minus 10 degrees!"),

            array('locale' => 'en', 'profile_detail_id' => 112, 'value' => "Trust, laughter and understanding"),

            array('locale' => 'en', 'profile_detail_id' => 113, 'value' => "Live and let live"),
            
            array('locale' => 'en', 'profile_detail_id' => 114, 'value' => "I love Jagjit Singh - so be ready for a ghazal session on our dates!"),

            array('locale' => 'en', 'profile_detail_id' => 115, 'value' => "When I forgot my wallet at home"),

            array('locale' => 'en', 'profile_detail_id' => 116, 'value' => "You can rock a traditional outfit"),

            array('locale' => 'en', 'profile_detail_id' => 117, 'value' => "They can make me laugh when I want to cry!"),

            array('locale' => 'en', 'profile_detail_id' => 118, 'value' => "I have never had a pizza in my life"),

            array('locale' => 'en', 'profile_detail_id' => 119, 'value' => "All political parties divide citizens, we need president's rule in the country"),
        );

        ProfileDetailTranslation::insert($profile_detail_trans);
    }
}
