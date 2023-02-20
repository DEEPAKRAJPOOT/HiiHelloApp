<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Aws\Rekognition\RekognitionClient;

if (!function_exists('verifyOTPLessAuth')) {
    function verifyOTPLessAuth($wa_id)
    {
        $host_url = config('utility.otp_less.host_url');
        $client_id = config('utility.otp_less.client_id');
        $client_secret = config('utility.otp_less.client_secret');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'clientId' => $client_id,
            'clientSecret' => $client_secret,
        ])->post($host_url, [
            'waId' => $wa_id
        ]);
        if ($response->status() === 200 && $response->json('statusCode') == 200) {
            return $response->json('data');
        }
        return $response->json('message');
    }
}

// Permission for admin panel
function getPermissions($user_type = 'normal')
{
    $permissions = array();

    if ($user_type == 'admin') {
        $permissions = [
            1 => [ // Dashboard
                'permissions' => 'access'
            ],
            2 => [ // Users
                'permissions' => 'access,view,add,edit,delete'
            ],
            3 => [ // Role Management
                'permissions' => 'access,add,edit,delete'
            ],
            4 => [ // Passion Management
                'permissions' => 'access,add,edit,delete'
            ],
            5 => [ // Country
                'permissions' => 'access,add,edit,delete'
            ],
            6 => [ // Profile Details
                'permissions' => 'access,add,edit,delete'
            ],
            7 => [ // Interests
                'permissions' => 'access,add,edit,delete'
            ],
            8 => [ // Locations
                'permissions' => 'access,add,edit,delete'
            ],
            9 => [ // Profile Reports
                'permissions' => 'access,view,edit'
            ],
            10 => [ // Faqs
                'permissions' => 'access,add,edit,delete'
            ],
            11 => [ // State
                'permissions' => 'access,add,edit,delete'
            ],
            12 => [ // Personality Types
                'permissions' => 'access,add,edit,delete'
            ],
            13 => [ // City
                'permissions' => 'access,add,edit,delete'
            ],
            14 => [ // Push Notification
                'permissions' => 'access,add'
            ],
            15 => [ // Subscription Plans
                'permissions' => 'access,add,edit,delete'
            ],
            16 => [ // Subscriptions
                'permissions' => 'access,view'
            ],
            17 => [ // Trasactions
                'permissions' => 'access,view'
            ],
            18 => [ // Call Logs
                'permissions' => 'access,view'
            ],
            19 => [ // App Details
                'permissions' => 'access,add,view'
            ],
            20 => [ // CMS Pages
                'permissions' => 'access,edit'
            ],
            21 => [ // Site Configurations
                'permissions' => 'access'
            ],
            22 => [ // User Under review
                'permissions' => 'access'
            ],
        ];
    }

    return $permissions;
}

// Call CURL
function fireCURL($url, $type, $data = NULL, $header = NULL)
{
    if (empty($header)) {
        $header = array("Content-Type:application/json");
    }

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
        CURLOPT_HTTPHEADER => $header,

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

function getUniqueString($table, $length = NULL)
{
    $length = $length ?? config('utility.custom_length', 8);
    return Str::random($length > 10 ? $length - 10 : 0) . time();
    // $field = 'custom_id';

    // $string = \Illuminate\Support\Str::random($length);
    // $found = \Illuminate\Support\Facades\DB::table($table)->where([$field => $string])->first();
    // if ($found) {
    //     return getUniqueString($table, $field, $length);
    // } else {
    //     return $string;
    // }
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


function checkAwsImageModeration($request, $image_param_name, $check_type = 'file')
{

    $image_arr_result = array();
    $api_status_code  = "";
    $user = $request->user();
    $min_confidence = config('utility.aws_image_moderation.min_confidence', 70);


    $client = new RekognitionClient([
        'region'    => 'ap-south-1',
        'version'   => 'latest'
    ]);


    if ($check_type == 'file') {
        //FILE OBJECT 
        $image = fopen($request->file($image_param_name)->getPathName(), 'r');
        $bytes = fread($image, $request->file($image_param_name)->getSize());
    } else {
        //image_param_name = S3 image url will be here as parameter if check type is url
        $image_path =   $image_param_name;
        $bytes = file_get_contents($image_path);
    }


    $moderate_image_results = $client->detectModerationLabels([
        'Image'         => ['Bytes' => $bytes],
        'MinConfidence' => $min_confidence
    ]);


    $cat_filter = config('utility.aws_image_moderation.category_filter', array());

    //CHECK FOR FACE DETECTION : HOW MAN FACE DETECTED.
    $result_face = $client->detectFaces([
        'Attributes' => ['ALL'], //ALL, DEFAULT
        'Image'         => ['Bytes' => $bytes],
    ]);

    if (count($result_face['FaceDetails']) == 0) {
        $image_arr_result["is_safe_image"] = false;
        $image_arr_result["face_detected_message"] = "Image have no face detected";
        $image_arr_result["log_message"] = "Image have no face detected";
        return $image_arr_result;
    } else if (count($result_face['FaceDetails']) >= 1) {
        $image_arr_result["face_detected_message"] = "";

        $DetectedLowAge = ceil(($result_face['FaceDetails'][0]['AgeRange']['Low'] + $result_face['FaceDetails'][0]['AgeRange']['High']) / 2) ?? 0;
        $leftEyeBrowUp_X = $result_face['FaceDetails'][0]['Landmarks'][7]['X'];
        $leftEyeBrowUp_Y = $result_face['FaceDetails'][0]['Landmarks'][7]['Y'];
        $rightEyeBrowUp_X = $result_face['FaceDetails'][0]['Landmarks'][10]['X'];
        $gender = $result_face['FaceDetails'][0]['Gender']['Value'];
        $mouthLeft_Y = $result_face['FaceDetails'][0]['Landmarks'][2]['Y'];
        $mouthRight_Y = $result_face['FaceDetails'][0]['Landmarks'][3]['Y'];

        $w = ($rightEyeBrowUp_X - $leftEyeBrowUp_X);
        $h = ($mouthLeft_Y - $leftEyeBrowUp_Y);
        $FWHR = ($w / $h);

        $image_arr_result["Facial_Width_Height_ratio"] = $FWHR;

        $width = ceil($result_face['FaceDetails'][0]['BoundingBox']['Width']);
        $height = ceil($result_face['FaceDetails'][0]['BoundingBox']['Height']);
        $aspect = ($width * $height);
    }
    dd($moderate_image_results);
    // if($result_face){


    // }

    $c_result = $client->recognizeCelebrities([
        'Image' => [ // REQUIRED
            //'Bytes' => file_get_contents("1.jpg"),
            'Bytes' => $bytes,
        ],
        'MaxLabels' => 10,
        'MinConfidence' => 20,
    ]);

    $text_result = $client->detectText([
        'Image' => [ // REQUIRED
            'Bytes' => $bytes,
        ],
        'MaxLabels' => 10,
        'MinConfidence' => 90,
    ]);
    // dd($moderate_image_results,$result_face,$c_result,$text_result);

    if (isset($moderate_image_results["@metadata"]) && $moderate_image_results["@metadata"]['statusCode'] == 200) {
        //response received then status code 200                            
        $api_status_code = "success";

        $log_message = "";


        if (count($moderate_image_results['ModerationLabels']) > 0) {

            $is_safe_image_category_filter = true;

            $filter_detail_message = "";


            foreach ($moderate_image_results['ModerationLabels'] as $cat_key => $res_data) {
                // code...
                //echo "<br> Category ".$res_data['Name'];
                //echo "<br> Parent Category ".$res_data['ParentName'];
                //echo "<br> Confidence ".$res_data['Confidence'];

                if (array_key_exists($res_data['Name'], $cat_filter)) {
                    // echo "<Br> in----".$cat_filter[$res_data['Name']];
                    // if($res_data['Confidence'] >)
                    if ($res_data['Confidence'] >= $cat_filter[$res_data['Name']]) {
                        //dd($cat_filter[$res_data['Name']]);
                        $is_safe_image_category_filter = false;
                        $filter_detail_message = $res_data['Name'] . " value in setting (" . $cat_filter[$res_data['Name']] . "). In response confidence value (" . $res_data['Confidence'] . ")";

                        $log_message = "Image Contain " . $res_data['Name'] . " With Confidence value " . $res_data['Confidence'];
                        break;
                    }
                }
            }
            $image_arr_result["is_safe_image"] = $is_safe_image_category_filter;
            $image_arr_result["moderation_labels_data"] = $filter_detail_message;
            $image_arr_result["log_message"] = $log_message;
        } else if (count($result_face['FaceDetails']) > 1) {
            $image_arr_result["is_safe_image"] = false;
            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = "Multiple faces detected";
        } else if (count($c_result['CelebrityFaces']) >= 1) {
            $image_arr_result["is_safe_image"] = false;

            $CelebrityName = $c_result['CelebrityFaces'][0]['Name'];

            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = "Celebrity face detected. Name: " . $CelebrityName;
        } else if (count($text_result['TextDetections']) >= 1) {
            $image_arr_result["is_safe_image"] = false;
            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = "Image has texts";
        } else if ($DetectedLowAge <= 15) {
            $image_arr_result["is_safe_image"] = false;
            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = "Age less than 15 detected";
        } else if ($gender && $gender !=  $user->gender) {
            $image_arr_result["is_safe_image"] = false;

            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = "Given image have different gender than user's gender";
        } else if (count($text_result['TextDetections']) >= 1) {
            $image_arr_result["is_safe_image"] = false;
            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = "Image has texts";
        } else {
            $image_arr_result["is_safe_image"] = true;
            $image_arr_result["moderation_labels_data"] = "";
            $image_arr_result["log_message"] = $log_message;
        }

        $image_arr_result["image_moderation_request"] = json_encode($moderate_image_results["@metadata"]);
        $image_arr_result["image_moderation_response"] = json_encode($moderate_image_results["ModerationLabels"]);
        $image_arr_result["moderation_response"] = $moderate_image_results["ModerationLabels"];

        /// CHECK FOR FACE DETECTION : HOW MAN FACE DETECTED.
        $image_arr_result["total_face_detected"] = count($result_face['FaceDetails']);



        return $image_arr_result;
    }
    return $image_arr_result;
}
