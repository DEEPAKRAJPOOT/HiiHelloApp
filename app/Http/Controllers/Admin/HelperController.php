<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class HelperController extends Controller
{
    public static function generateUrl($path)
    {
        // dd($path);
        $url = "";
        if( !empty($path) && Storage::disk('public')->exists($path) )

            $url = Storage::url($path);

        return $url;
    }

}
