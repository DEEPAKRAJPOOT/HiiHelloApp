<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\InterestTranslation;

class InterestTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        InterestTranslation::truncate();
        Schema::enableForeignKeyConstraints();

        $interest_trans = array(
            array('locale' => 'en', 'interest_id' => 1, 'title' => "Traveling", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 2, 'title' => "Food", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 3, 'title' => "Music", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 4, 'title' => "Reading", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 5, 'title' => "Film & TV", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 6, 'title' => "Favourite Movie Stars", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 7, 'title' => "Hobbies", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 8, 'title' => "Favourite Childhood Games", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 9, 'title' => "Sports", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 10, 'title' => "Backpacking", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 11, 'title' => "Beaches", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 12, 'title' => "Camping", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 13, 'title' => "Explore New Cities", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 14, 'title' => "Hiking Trips", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 15, 'title' => "Road Trips", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 16, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 17, 'title' => "Biryani", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 18, 'title' => "Dal Chawal", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 19, 'title' => "Pulav", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 20, 'title' => "Butter Chicken", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 21, 'title' => "Paneer Makhni", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 22, 'title' => "Egg Curry", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 23, 'title' => "Coffee", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 24, 'title' => "Tea", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 25, 'title' => "Maggi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 26, 'title' => "Pizza", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 27, 'title' => "Kebab", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 28, 'title' => "Pasta", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 29, 'title' => "Rajma Chawal", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 30, 'title' => "Vada Pav", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 31, 'title' => "Idli Dosa", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 32, 'title' => "Ice Cream", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 33, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 34, 'title' => "Bollywood", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 35, 'title' => "Classical", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 36, 'title' => "Ghazal", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 37, 'title' => "Karnataki", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 38, 'title' => "Country", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 39, 'title' => "Electronic", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 40, 'title' => "Pop", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 41, 'title' => "Rock", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 42, 'title' => "Folk", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 43, 'title' => "Indie", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 44, 'title' => "Punjabi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 45, 'title' => "R&B", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 46, 'title' => "Rap", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 47, 'title' => "Sufi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 48, 'title' => "Soul", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 49, 'title' => "Jazz", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 50, 'title' => "Blues", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 51, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 52, 'title' => "Action Adventure", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 53, 'title' => "Biographies", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 54, 'title' => "Classics", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 55, 'title' => "Comedy", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 56, 'title' => "Fantasy", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 57, 'title' => "Crime", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 58, 'title' => "History", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 59, 'title' => "Horror", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 60, 'title' => "Romance", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 61, 'title' => "Thriller", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 62, 'title' => "Science", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 63, 'title' => "Technology", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 64, 'title' => "Self Help", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 65, 'title' => "Sci-Fi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 66, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 67, 'title' => "Documentaries", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 68, 'title' => "Action Adventure", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 69, 'title' => "Animated", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 70, 'title' => "Bollywood", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 71, 'title' => "Hollywood", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 72, 'title' => "Comedy", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 73, 'title' => "Crime", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 74, 'title' => "Drama", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 75, 'title' => "Fantasy", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 76, 'title' => "Game Shows", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 77, 'title' => "Reality Shows", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 78, 'title' => "Horror", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 79, 'title' => "Rom-Com", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 80, 'title' => "Sci-Fi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 81, 'title' => "Superhero", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 82, 'title' => "Thriller", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 83, 'title' => "Romance", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 84, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 85, 'title' => "Shahrukh Khan", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 86, 'title' => "Arjun Kapoor", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 87, 'title' => "Rajnikanth", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 88, 'title' => "Aishwarya Rai", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 89, 'title' => "Aamir Khan", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 90, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),


            array('locale' => 'en', 'interest_id' => 91, 'title' => "Farming", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 92, 'title' => "Knitting", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 93, 'title' => "Mehendi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 94, 'title' => "Gardening", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 95, 'title' => "Baking", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 96, 'title' => "Cooking", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 97, 'title' => "Video Games", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 98, 'title' => "Making Tik Tok Videos", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 99, 'title' => "Reading", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 100, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),
            
            array('locale' => 'en', 'interest_id' => 101, 'title' => "Antakshari", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 102, 'title' => "Pakdam Pakdi", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 103, 'title' => "Dumb Charades", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 104, 'title' => "Marbles", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 105, 'title' => "Snakes And Ladders", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 106, 'title' => "Ludo", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 107, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),

            array('locale' => 'en', 'interest_id' => 108, 'title' => "Athletics", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 109, 'title' => "Badminton", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 110, 'title' => "Baseball", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 111, 'title' => "Basketball", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 112, 'title' => "Boxing", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 113, 'title' => "Cricket", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 114, 'title' => "Cycling", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 115, 'title' => "Football", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 116, 'title' => "Hockey", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 117, 'title' => "Horse Riding", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 118, 'title' => "Martial Arts", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 119, 'title' => "Running", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 120, 'title' => "Swimming", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 121, 'title' => "Tennis", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 122, 'title' => "Volleyball", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 123, 'title' => "Yoga", 'created_at' => now(), 'updated_at' => now()),
            array('locale' => 'en', 'interest_id' => 124, 'title' => "Others", 'created_at' => now(), 'updated_at' => now()),
        );

        InterestTranslation::insert($interest_trans);
    }
}
