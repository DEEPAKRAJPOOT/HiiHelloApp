<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Interest;
use App\Models\Location;
use App\Models\Personality;
use App\Models\ProfileDetail;

class AdminLangTraslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:default-trans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command used to translate default language to other language so empty data not come in apis.';

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
        $language_alloweds  =   ['en', 'hi', 'ta', 'mr', 'bn', 'gu', 'kn', 'ml', 'or', 'pa', 'te'];
        $default_lang_code  =   config('utility.default_lang_code');
        $message            =   'No  Admin details foundfor translated !!!';

        Interest::with('interestTranslations')->chunk(100, function($interests) use ($default_lang_code, $language_alloweds) {
            if($interests->isNotEmpty()){
                foreach($interests as $interest){
                    if($interest->interestTranslations->isNotEmpty()){
                        $default_data = NULL; $available_langs = $new_details = [];
                        $message = 'Interest details translated successfully !!!';

                        foreach($interest->interestTranslations as $interestTranslation){
                            if(!in_array($interestTranslation->locale, $available_langs)){
                                array_push($available_langs, $interestTranslation->locale);
                            }

                            if($default_lang_code == $interestTranslation->locale){
                                $default_data = $interestTranslation;
                            }
                        }

                        if(count($available_langs) > 0 && $default_data != NULL){
                            foreach($language_alloweds as $language_allowed){
                                if(!in_array($language_allowed, $available_langs)){
                                    $new_details[$language_allowed]['title'] = $default_data->title;
                                }
                            }
                        }

                        if(count($new_details) > 0){
                            $interest->update($new_details);
                        }
                    }
                }
            }
        });

        Location::with('locationTranslations')->chunk(100, function($locations) use ($default_lang_code, $language_alloweds) {
            if($locations->isNotEmpty()){
                foreach($locations as $location){
                    if($location->locationTranslations->isNotEmpty()){
                        $default_data = NULL; $available_langs = $new_details = [];

                        foreach($location->locationTranslations as $locationTranslation){
                            if(!in_array($locationTranslation->locale, $available_langs)){
                                array_push($available_langs, $locationTranslation->locale);
                            }

                            if($default_lang_code == $locationTranslation->locale){
                                $default_data = $locationTranslation;
                            }
                        }

                        if(count($available_langs) > 0 && $default_data != NULL){
                            foreach($language_alloweds as $language_allowed){
                                if(!in_array($language_allowed, $available_langs)){
                                    $new_details[$language_allowed]['name'] = $default_data->name;
                                }
                            }
                        }

                        if(count($new_details) > 0){
                            $location->update($new_details);
                            $message = 'Location details translated successfully !!!';
                        }
                    }
                }
            }
        });

        Personality::with('personalityTranslations')->chunk(100, function($personalities) use ($default_lang_code, $language_alloweds) {
            if($personalities->isNotEmpty()){
                foreach($personalities as $personality){
                    if($personality->personalityTranslations->isNotEmpty()){
                        $default_data = NULL; $available_langs = $new_details = [];

                        foreach($personality->personalityTranslations as $personalityTranslation){
                            if(!in_array($personalityTranslation->locale, $available_langs)){
                                array_push($available_langs, $personalityTranslation->locale);
                            }

                            if($default_lang_code == $personalityTranslation->locale){
                                $default_data = $personalityTranslation;
                            }
                        }

                        if(count($available_langs) > 0 && $default_data != NULL){
                            foreach($language_alloweds as $language_allowed){
                                if(!in_array($language_allowed, $available_langs)){
                                    $new_details[$language_allowed]['title'] = $default_data->title;
                                    $new_details[$language_allowed]['description'] = $default_data->description;
                                }
                            }
                        }

                        if(count($new_details) > 0){
                            $personality->update($new_details);
                            $message = 'Personality details translated successfully !!!';
                        }
                    }
                }
            }
        });

        ProfileDetail::with('profileDetailTranslations')->chunk(100, function($profile_details) use ($default_lang_code, $language_alloweds) {
            if($profile_details->isNotEmpty()){
                foreach($profile_details as $profile_detail){
                    if($profile_detail->profileDetailTranslations->isNotEmpty()){
                        $default_data = NULL; $available_langs = $new_details = [];

                        foreach($profile_detail->profileDetailTranslations as $profileDetailTranslation){
                            if(!in_array($profileDetailTranslation->locale, $available_langs)){
                                array_push($available_langs, $profileDetailTranslation->locale);
                            }

                            if($default_lang_code == $profileDetailTranslation->locale){
                                $default_data = $profileDetailTranslation;
                            }
                        }

                        if(count($available_langs) > 0 && $default_data != NULL){
                            foreach($language_alloweds as $language_allowed){
                                if(!in_array($language_allowed, $available_langs)){
                                    $new_details[$language_allowed]['value'] = $default_data->value;
                                }
                            }
                        }

                        if(count($new_details) > 0){
                            $profile_detail->update($new_details);
                            $message = 'Profile Section details translated successfully !!!';
                        }
                    }
                }
            }
        });

        $this->info($message);
        return $message;
    }
}
