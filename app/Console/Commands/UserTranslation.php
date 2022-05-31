<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UserTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User details stored in multi language';

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
        $apiKey = 'AIzaSyCnTLblh4He46O3-5NoJ0sXOzyelS76jEY';
        $text = 'Hello world!';
        //for translate
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target=te&target=gu';

        //for detect
        // $url = 'https://translation.googleapis.com/language/translate/v2/detect?key=AIzaSyCnTLblh4He46O3-5NoJ0sXOzyelS76jEY&q=helloworld';

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseDecoded = json_decode($response, true);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
              //Here we fetch the HTTP response code
        dd($responseCode);
        curl_close($handle);
        
        if($responseCode != 200) {
            dump('Fetching translation failed! Server response code:' . $responseCode);
            dd('Error description: ' . $responseDecoded['error']['errors'][0]['message']);
        }
        else {
            dump('Source: ' . $text);
            dd($responseDecoded);
            dd('Translation: ' . $responseDecoded['data']['translations'][0]['translatedText']);
        }
        
    }
}
