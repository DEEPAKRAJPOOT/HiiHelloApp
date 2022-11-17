<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to translate the details into multiple languages using third party google translation api & update the details into the database.';

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

        // $users = User::select('id','custom_id','language_id','is_trans_full_name','is_trans_about_me','is_trans_fav_movie')
        //                         ->with('userTranslations','language')
        //                         ->where('is_trans_full_name','n')->orWhere('is_trans_about_me','n')
        //                         ->orWhere('is_trans_fav_movie','n')->get();

        User::select('id','custom_id','language_id','is_trans_full_name','is_trans_about_me','is_trans_fav_movie')
                                ->with('userTranslations','language')
                                ->where('is_trans_full_name','n')->orWhere('is_trans_about_me','n')
                                ->orWhere('is_trans_fav_movie','n')
                                ->chunk(100, function($users) use ($language_alloweds, $apiKey, $message) {
            if($users->isNotEmpty()){
                foreach($users as $user){
                    if($user->userTranslations->isNotEmpty()){
                        $detected_lang = $user->language ? $user->language->lang_code : 'en';
                        // $detected_lang = $user->userTranslations[0] ? $user->userTranslations[0]->locale : $detected_lang;

                        if($user->userTranslations[0]){
                            $full_name  =   $user->userTranslations[0]->full_name;
                            $about_me   =   $user->userTranslations[0]->about_me;
                            $fav_movie  =   $user->userTranslations[0]->fav_movie;

                            if( !empty($full_name) && $user->is_trans_full_name == 'n'){
                                $message = $this->translateText($apiKey, $language_alloweds, $user, $detected_lang, 'full_name', $full_name );
                                $user->is_trans_full_name = 'y';
                            }
                            if( !empty($about_me) && $user->is_trans_about_me == 'n'){
                                $message = $this->translateText($apiKey, $language_alloweds, $user, $detected_lang, 'about_me', $about_me );
                                $user->is_trans_about_me = 'y';
                            }
                            if( !empty($fav_movie) && $user->is_trans_fav_movie == 'n'){
                                $message = $this->translateText($apiKey, $language_alloweds, $user, $detected_lang, 'fav_movie', $fav_movie );
                                $user->is_trans_fav_movie = 'y';
                            }

                            $user->save();
                        }
                    }
                }
            }
        });

        // $this->info($message);
        return $message;
    }

    function translateText($apiKey, $language_alloweds, $user, $detected_lang, $column, $text )
    {
        $message = 'No details found to translate !!!';

        // Detect Language
        // $detect_url = 'https://translation.googleapis.com/language/translate/v2/detect?key=' .$apiKey. '&q='.rawurlencode($text);
        // $handle = curl_init($detect_url);
        // curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($handle);
        // $responseDecoded = json_decode($response, true);
        // $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
        // curl_close($handle);

        // if($responseCode == 200) {
        //     if($responseDecoded['data'] && $responseDecoded['data']['detections'] && $responseDecoded['data']['detections'][0] && $responseDecoded['data']['detections'][0][0] && $responseDecoded['data']['detections'][0][0]['language']){
                
        //         $detected_lang = $responseDecoded['data']['detections'][0][0]['language'] ?? $detected_lang;
        //     }
        // }

        foreach($language_alloweds as $language_allowed){

            // Translate Language
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
                $translatedText = $responseDecoded['data']['translations'][0]['translatedText'];

                $transaction_data = [
                    $language_allowed    =>  [
                        $column =>  $translatedText,
                    ],
                ];
                $user->update($transaction_data);

                // Store Account Id
                if($column == 'full_name' && $language_allowed == 'en'){
                    $user->account_id = Str::slug(substr($translatedText, 0, 4), "_").'_'.time();
                }

                $user->save();
                
                $message = 'User Id : '.$user->id.' details translated successfully !!!';
            }
        }

        return $message;
    }
}
