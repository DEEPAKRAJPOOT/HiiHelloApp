<?php

// Permission for admin panel
function getPermissions($user_type = 'normal')
{
    $permissions = array();

    if ($user_type == 'admin') {
        $permissions = [
            1 => [                                                   // Dashboard
                'permissions' => 'access'
            ],
            2 => [                                                   // Users
                'permissions' => 'access,add,edit,delete'
            ],
            3 =>[ //Role Management
                'permissions' => 'access,add,edit,delete'
            ],
            4 =>[ //Country
                'permissions' => 'access,add,edit,delete'
            ],
            5 =>[ //State
                'permissions' => 'access,add,edit,delete'
            ],
            6 =>[ //City
                'permissions' => 'access,add,edit,delete'
            ],
            7 =>[ //CMS Pages
                'permissions' => 'access,edit'
            ],
            8 =>[ //Site Configurations
                'permissions' => 'access'
            ],
        ];
    }

    return $permissions;
}

// Call CURL
function fireCURL($url, $type, $data = NULL)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => strtoupper($type),
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array("Content-Type:application/json"),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return (object) json_decode($response, true);
}

// Show number is cool format, like 1K, 2M, 50K etc
function number_format_short($n, $precision = 1)
{
    if ($n < 900) {
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
    if ($precision > 0) {
        $dotzero = '.' . str_repeat('0', $precision);
        $n_format = str_replace($dotzero, '', $n_format);
    }
    return $n_format . $suffix;
}

function getUniqueString($table, $length = NULL){
    $length = $length ?? config('utility.custom_length', 8);
    $field = 'custom_id';

    $string = \Illuminate\Support\Str::random($length);
    $found = \Illuminate\Support\Facades\DB::table($table)->where([$field => $string])->first();
    if ($found) {
        return getUniqueString($table, $field, $length);
    } else {
        return $string;
    }
}

function generateURL($file = "")
{
    // dd($file);
    return App\Http\Controllers\Admin\HelperController::generateUrl($file);
}
function get_guard()
{
    //You Need to define all guard created
    if (\Auth::guard('admin')->check()) {
        return "admin";
    } elseif (\Auth::guard('web')->check()) {
        return "user";
    } else {
        return "Guard not match";
    }
}
