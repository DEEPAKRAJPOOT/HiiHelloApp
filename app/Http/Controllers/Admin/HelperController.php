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
        if (!empty($path))
            $path = ltrim($path, '/');

        if (!empty($path) && Storage::exists($path))
            $url = Storage::url($path);

        // $url = Storage::temporaryUrl( $path, now()->addMinutes(5) );

        // For AWS CDN
        $s3_bucket_url = config('utility.s3.prefix_url');
        $file_system = config('filesystems.default');
        if (!empty($path) && !empty($s3_bucket_url) && !empty($file_system) && $file_system  == 's3') {
            // $url = Storage::disk('s3')->url($path);
            $url = $s3_bucket_url .'/'. $path;
        }

        return $url;
    }
}
