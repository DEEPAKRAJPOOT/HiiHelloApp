<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Location;
use Illuminate\Support\Str;

class LocationTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:location';

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
        $language_alloweds  =   ['en', 'hi', 'ta', 'mr', 'bn', 'gu', 'kn', 'ml', 'or', 'pa', 'te', 'as'];
        $default_lang_code  =   config('utility.default_lang_code');
        $apiKey             =   config('utility.google.translate.api_key');
        $message            =   'No details found to translate !!!';
        $message            =   "No location translate records found.";


        $locations = Location::where('is_trans_name','n')
                // ->orWhere('is_trans_locality','n')
                // ->orWhere('is_trans_state','n')
                // ->whereNotNull('locality')
                // ->whereNotNull('state')
                // ->where('id','258')
                ->with('locationTranslations')
                ->get();
            // echo "<pre>"; print_r($locations->toArray()); die();
                // ->chunk(100, function($locations) use ($default_lang_code, $language_alloweds, $apiKey, $message) {
            if($locations->isNotEmpty()){
                foreach($locations as $location){
                    if($location->locationTranslations->isNotEmpty()){

                        if($location->locationTranslations[0]){
                            $name           =   $location->locationTranslations[0]->name;
                            $locality       =   $location->locationTranslations[0]->locality;
                            $state          =   $location->locationTranslations[0]->state;
                            $detected_lang  =   $location->locationTranslations[0] ? $location->locationTranslations[0]->locale : $default_lang_code;
                            $texxt = $name.",".$state;
                            if( !empty($name) && $location->is_trans_name == 'n'){
                                $success = $this->translateText($apiKey, $language_alloweds, $location, $detected_lang, 'name','state', $texxt);
                                if($success){ 
                                    $location->is_trans_name = 'y'; 
                                    $location->is_trans_state = 'y'; 
                                }
                            }
                            // if( !empty($locality) && $location->is_trans_locality == 'n'){
                            //     $success = $this->translateText($apiKey, $language_alloweds, $location, $detected_lang, 'locality', $locality );
                            //     if($success){ 
                            //         $location->is_trans_locality = 'y'; 
                            //     }
                            // }
                            // if( !empty($state) && $location->is_trans_state == 'n'){
                            //     $success = $this->translateText($apiKey, $language_alloweds, $location, $detected_lang, 'state', $state );
                            //     if($success){ 
                            //         $location->is_trans_state = 'y'; 
                            //     }
                            // }
                            $location->save();
                        }
                    }
                }
                 $message = 'Location translate successfully.';
            }
        // });

       return $message;
    }

    function translateText($apiKey, $language_alloweds, $module, $detected_lang, $column1, $column2, $text )
    {
        $success = false;
        foreach($language_alloweds as $language_allowed){
            //Translate Language
            // if ($detected_lang == $language_allowed) {
            //     $traslate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target='.$language_allowed;
            // }
            // else
            // {
            //     $traslate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source='.$detected_lang.'&target='.$language_allowed;
            // }

            $traslate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&target='.$language_allowed;

            $handle = curl_init($traslate_url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);
            $responseDecoded = json_decode($response, true);
            $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
            curl_close($handle);

            if($responseCode == 200) {
                $translatedText = explode(",",$responseDecoded['data']['translations'][0]['translatedText']);
                $transaction_data = [
                    $language_allowed    =>  [
                        $column1 =>  trim($translatedText[0]),
                        $column2 =>  isset($translatedText[1]) ? trim($translatedText[1]) : NULL,
                    ],
                ];
                $module->update($transaction_data);
                $module->save();
                $success = true;
            }

        }

        return $success;
    }
}
