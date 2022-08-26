<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
{
    public static function generateUrl($path)
    {
        $url = "";
        if( !empty($path) )
            $path = ltrim($path, '/');

        if( !empty($path) && Storage::exists($path) )
            $url = Storage::url($path);

        // $url = Storage::temporaryUrl( $path, now()->addMinutes(5) );

        // For AWS CDN
        if( !empty(env('AWS_URl')) && !empty(env('FILESYSTEM_DRIVER')) && env('FILESYSTEM_DRIVER') == 's3' ){
            // $url = Storage::disk('s3')->url($path);
            $url = env('AWS_URl').$path;
        }

        return $url;
    }
}
