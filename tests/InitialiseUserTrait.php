<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Tests\InitialiseUserSimpleTrait;
use Illuminate\Http\UploadedFile;
use App\Models\Country;
use App\Models\Location;
use App\Models\Interest;
use App\Models\Language;

trait InitialiseUserTrait
{
    use InitialiseUserSimpleTrait;

    /** @var User */
    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function setCountry(){
        $faker = \Faker\Factory::create('en_UK');
        $data = [
            'en'    =>  [
                'name'      =>  $faker->name,
            ],
            'code'          =>  'IN',
            'phonecode'     =>  91,
            'custom_id'     =>  getUniqueString('countries'),
        ];
        $country = Country::create($data);
        return $country;
    }

    protected function setLocation(){
        $faker = \Faker\Factory::create('en_UK');
        $data = [
            'en'    =>  [
                'name'  =>  $faker->name,
            ],
            'custom_id' => getUniqueString('locations'),
        ];
        $location = Location::create($data);
        return $location;
    }

    protected function setInterest(){
        $faker = \Faker\Factory::create('en_UK');
        $data = [
            'en'    =>  [
                'title'  =>  $faker->title,
            ],
            'custom_id' => getUniqueString('interests'),
        ];
        $interest = Interest::create($data);
        return $interest;
    }

    protected function getUser()
    {
        $faker          =   \Faker\Factory::create('en_UK');
        $country        =   $this->setCountry();
        $location       =   $this->setLocation();
        $interest       =   $this->setInterest();
        $language       =   Language::whereIsActive('y')->firstOrFail();

        $user = [
            'x-language'        =>  config('utility.default_lang_code'),
            'first_name'        =>  'Abc',
            'last_name'         =>  'Xyz',
            'email'             =>  'test1@gmail.com',
            'country_code'      =>  $country->phonecode,
            'contact_no'        =>  12345678,
            'birth_date'        =>  '01/05/2000',
            'gender'            =>  'Male',
            'interest'          =>  'Female',
            'location'          =>  $location->custom_id,
            'interests'         =>  [ $interest->custom_id ],                                   // Array
            'language'          =>  $language->lang_code,
            'profile_photo'     =>  UploadedFile::fake()->image('profile_photo_1.jpg'),
            'images'            =>  [ UploadedFile::fake()->image('profile_image_1.jpg') ],     // Array
        ];

        return $user;
    }
}
