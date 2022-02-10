<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Sightengine\SightengineClient;

class ImageModeration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:moderation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to check the nudity content of images using third party api called sightengine & remove thoes images from the database.';

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
        // $message = "No Moderation Image Found.";
        // try{
        //     $api_url    =   config('utility.image_moderation.api_url');
        //     $api_user   =   config('utility.image_moderation.api_user');
        //     $api_secret =   config('utility.image_moderation.api_secret');
        //     $models     =   'nudity'; // We can also pass array if we have multiple models

        //     // $image_path = 'http://127.0.0.1:8000/storage/users/profile_photo/CUfeMOfu2B7qFeKgdgfRqIDJmgHpNwOSP3c4i9CW.jpg';
        //     $image_path = 'http://la.webdevprojects.cloud/hi-hello/storage/users/profile_photo/gXQrpAaROu893nzhuS6uI7JdRFcHcYq2musi2Y7r.jpg';

        //     $client     =   new \GuzzleHttp\Client();
        //     $file       =   fopen($image_path, 'r');
        //     $response   =   $client->request('POST', $api_url, 
        //                     [
        //                         'query' => [
        //                             'api_user'      =>  $api_user,
        //                             'api_secret'    =>  $api_secret,
        //                             'models'        =>  $models
        //                         ],
        //                         'multipart' => [
        //                             [
        //                                 'name'      =>  'media',
        //                                 'contents'  =>  $file
        //                             ]
        //                         ]
        //                     ]); 

        //     $output = json_decode($response->getBody());
        //     if($output->status == 'success'){
        //         if($output->nudity){
        //             $row            =   $output->nudity->raw;
        //             $safe           =   $output->nudity->safe;
        //             $partial        =   $output->nudity->partial;
        //             $safe_image     =   true;

        //             $row_condition      =   $row > $row_value;
        //             $partial_condition  =   $partial > $partial_value;
        //             $safe_condition     =   $safe < $safe_value;

        //             if($row_condition || $partial_condition || $safe_condition){
        //                 $safe_image = false;
        //             }

        //             dump($row_condition, $partial_condition , $safe_condition);
        //             dd("Safe",$safe_image,$row,$safe,$partial);
        //         }
        //         dd("output",$output);
        //     }
        //     dd("final",$output);
        // } catch (\Exception $e) {
        //     // Add error log
        //     $file = 'image_moderation';
        //     $iqTrackingLog = new Logger($file);
        //     $iqTrackingLog->pushHandler(new StreamHandler(storage_path('logs/' . $file . '.log')), Logger::ERROR);
        //     $iqTrackingLog->error($file, ['error' => $e->getMessage()]);
        // }
        // return $message;
    }


    // Information As Per Documentation
    // LINK :: https://sightengine.com/docs/nsfw-detection-model

    // 1) RAW NUDITY
    //    ->    Decimal between 0 and 1. Images with a value close to 1 are images with a high probability of containing raw nudity while images with a probability closer to 0 have a lower probability of containing raw nudity.

    // 2) PARTIAL NUDITY
    //     ->  Decimal between 0 and 1. Images with a value close to 1 are images with a high probability of containing partial nudity while images with a probability closer to 0 have a lower probability of containing partial nudity.
    //         If this value is larger than nudity.raw nudity.safe then a nudity.partial_tag field will be added to describe the type of partial nudity found.

    // 3) SAFE
    //     ->  Decimal between 0 and 1. Images with a value close to 1 are images with a high probability of being safe (i.e. no nudity) while images with a probability closer to 0 have a lower probability of being safe.

}   
