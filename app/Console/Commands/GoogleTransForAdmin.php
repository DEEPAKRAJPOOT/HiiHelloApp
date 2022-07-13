<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Personality;
use App\Models\Interest;
use App\Models\Location;
use App\Models\ProfileDetail;

class GoogleTransForAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:admin-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to translate the admin details using third party google translation api.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $language_alloweds  =   ['en', 'hi', 'ta', 'mr', 'bn', 'gu', 'kn', 'ml', 'or', 'pa', 'te'];
        $language_alloweds  =   ['en', 'hi'];
        $default_lang_code  =   config('utility.default_lang_code');
        $apiKey             =   config('utility.google.translate.api_key');
        $message            =   'No admin details found to translate !!!';

        Personality::with('personalityTranslations')->chunk(100, function($personalities) use ($default_lang_code, $language_alloweds, $apiKey, $message) {
            if($personalities->isNotEmpty()){
                foreach($personalities as $personality){
                    if($personality->personalityTranslations->isNotEmpty()){

                        if($personality->personalityTranslations[0]){
                            $title          =   $personality->personalityTranslations[0]->title;
                            $description    =   $personality->personalityTranslations[0]->description;
                            $detected_lang  =   $personality->personalityTranslations[0] ? $personality->personalityTranslations[0]->locale : $default_lang_code;

                            if( !empty($title) && $personality->is_trans_title == 'n'){
                                $success = $this->translateText($apiKey, $language_alloweds, $personality, $detected_lang, 'title', $title );
                                if($success){ 
                                    $personality->is_trans_title = 'y'; 
                                    $message = 'Personality details translatated successfully !!!';
                                }
                            }
                            if( !empty($description) && $personality->is_trans_description == 'n'){
                                $success = $this->translateText($apiKey, $language_alloweds, $personality, $detected_lang, 'description', $description );
                                if($success){
                                    $personality->is_trans_description = 'y'; 
                                    $message = 'Personality details translatated successfully !!!';
                                }
                            }
                            $personality->save();
                        }
                    }
                }
            }
        });

        Interest::with('interestTranslations')->chunk(100, function($interests) use ($default_lang_code, $language_alloweds, $apiKey, $message) {
            if($interests->isNotEmpty()){
                foreach($interests as $interest){
                    if($interest->interestTranslations->isNotEmpty()){

                        if($interest->interestTranslations[0]){
                            $title          =   $interest->interestTranslations[0]->title;
                            $detected_lang  =   $interest->interestTranslations[0] ? $interest->interestTranslations[0]->locale : $default_lang_code;

                            if( !empty($title) && $interest->is_trans_title == 'n'){
                                $success = $this->translateText($apiKey, $language_alloweds, $interest, $detected_lang, 'title', $title );
                                if($success){ 
                                    $interest->is_trans_title = 'y'; 
                                    $message = 'Interest details translatated successfully !!!';
                                }
                            }
                            $interest->save();
                        }
                    }
                }
            }
        });

        Location::with('locationTranslations')->chunk(100, function($locations) use ($default_lang_code, $language_alloweds, $apiKey, $message) {
            if($locations->isNotEmpty()){
                foreach($locations as $location){
                    if($location->locationTranslations->isNotEmpty()){

                        if($location->locationTranslations[0]){
                            $name           =   $location->locationTranslations[0]->name;
                            $detected_lang  =   $location->locationTranslations[0] ? $location->locationTranslations[0]->locale : $default_lang_code;

                            if( !empty($name) && $location->is_trans_name == 'n'){
                                $success = $this->translateText($apiKey, $language_alloweds, $location, $detected_lang, 'name', $name );
                                if($success){ 
                                    $location->is_trans_name = 'y'; 
                                    $message = 'Location details translatated successfully !!!';
                                }
                            }
                            $location->save();
                        }
                    }
                }
            }
        });

        ProfileDetail::with('profileDetailTranslations')->chunk(100, function($profile_details) use ($default_lang_code, $language_alloweds, $apiKey, $message) {
            if($profile_details->isNotEmpty()){
                foreach($profile_details as $profile_detail){
                    if($profile_detail->profileDetailTranslations->isNotEmpty()){

                        if($profile_detail->profileDetailTranslations[0]){
                            $value          =   $profile_detail->profileDetailTranslations[0]->value;
                            $detected_lang  =   $profile_detail->profileDetailTranslations[0] ? $profile_detail->profileDetailTranslations[0]->locale : $default_lang_code;

                            if( !empty($value) && $profile_detail->is_trans_value == 'n'){
                                $success = $this->translateText($apiKey, $language_alloweds, $profile_detail, $detected_lang, 'value', $value );
                                if($success){ 
                                    $profile_detail->is_trans_value = 'y'; 
                                    $message = 'Profile Section details translatated successfully !!!';
                                }
                            }
                            $profile_detail->save();
                        }
                    }
                }
            }
        });

        $this->info($message);
        return $message;
    }

    function translateText($apiKey, $language_alloweds, $module, $detected_lang, $column, $text )
    {
        $success = false;
        foreach($language_alloweds as $language_allowed){
            if($detected_lang != $language_allowed){
                
                // Translate Language
                $traslate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source='.$detected_lang.'&target='.$language_allowed;

                $handle = curl_init($traslate_url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($handle);
                $responseDecoded = json_decode($response, true);
                $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
                curl_close($handle);

                if($responseCode == 200) {
                    $translatedText = $responseDecoded['data']['translations'][0]['translatedText'];

                    $transaction_data = [
                        $language_allowed    =>  [
                            $column =>  $translatedText,
                        ],
                    ];
                    $module->update($transaction_data);
                    $module->save();
                    $success = true;
                }
            }
        }

        return $success;
    }
}
