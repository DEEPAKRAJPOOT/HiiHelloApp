<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Tests\InitialiseUserSimpleTrait;
use Illuminate\Http\UploadedFile;
use App\Models\Country;
use App\Models\Location;
use App\Models\Interest;
use App\Models\Language;
use App\Models\Faq;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;

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

    protected function getVersion(){ return "v.1.0"; }

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
        $location = $this->setLocation();
        $data = [
            'en'    =>  [
                'title'         =>  $faker->title,
            ],
            'custom_id'     =>  getUniqueString('interests'),
            'location_id'   =>  $location->id,
            'level'         =>  2,
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
            'custom_id'         =>  getUniqueString('users'),
            'x-language'        =>  config('utility.default_lang_code'),
            // 'full_name'         =>  $faker->firstNameFemale.' '.$faker->lastName,
            'first_name'        =>  $faker->firstNameFemale,
            'last_name'         =>  $faker->lastName,
            'email'             =>  $faker->email,
            'country_code'      =>  $country->phonecode,
            'contact_no'        =>  12345678,
            'birth_date'        =>  '2000-05-01',
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

    protected function setFaq(){
        $faker = \Faker\Factory::create('en_UK');
        $data = [
            'en'    =>  [
                'question'      =>  'This is test question ?',
                'answer'        =>  'Yes, This is test answer.',
            ],
            'custom_id'     =>  getUniqueString('faqs'),
        ];
        $faq = Faq::create($data);
        return $faq;
    }

    public function createUser()
    {
        $user = $this->getUser();
        $user = User::create($user);
        $user->save();
        return $user;
    }

    protected function getActiveUser(){
        $user = User::whereIsActive('y')->latest()->firstOrFail();
        return $user;
    }

    protected function setUserToken($user){
        $token = $user->createToken(config('utility.token'))->plainTextToken;
        $this->withHeader('Authorization', 'Bearer ' . $token);
        
        return $user;
    }

    protected function getLocation(){
        $interest = Interest::whereNotNull('location_id')->latest()->firstOrFail();
        return Location::whereId($interest->location_id)->firstOrFail();
    }

    protected function getChatRoomUser(){
        $chat_room = ChatRoom::firstOrFail();
        $user = User::whereId($chat_room->creator_id)->whereIsActive('y')->firstOrFail();
        return $user;
    }

    protected function getChatRoom(){
        return ChatRoom::firstOrFail();
    }

    protected function storeNewChatMessage($chat_room){
        $chat_message = ChatMessage::create([
            'custom_id'     =>  getUniqueString('chat_messages'),
            'room_id'       =>  $chat_room->id,
            'sender_id'     =>  $chat_room->creator_id,
            'receiver_id'   =>  $chat_room->participate_id,
            'message'       =>  '{ "type" : "text", "value" : "Hello", "others" : "[]" }',
        ]);

        return $chat_message;
    }
}
