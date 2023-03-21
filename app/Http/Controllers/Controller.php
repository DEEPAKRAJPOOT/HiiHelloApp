<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Models\ApiLogs;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //add for api
    public $response = [
        'data'  =>  null,
        'meta'  =>  [
            'url'   =>  "",
            'api'   =>  "",
            'message'   =>  "",
            'is_subscribed' =>  false,
            'subscription_end_date' =>  "",
        ],
    ];

    // protected $response = array('data' => null, 'message' => '');
    // protected $status = 422;
    // protected $statusArr = [
    //     'success' => 200,
    //     'not_found' => 404,
    //     'unauthorised' => 412,
    //     'already_exist' => 409,
    //     'validation' => 422,
    //     'something_wrong' => 405,
    // ];

    public $status = 412;

    // Instead Of this Now Using Response.php Provided By Laravel
    // public $statusArr = [
    //     'success'=> 200,
    //     'bad_request' => 400,
    //     'authorization_required' => 401,
    //     'payment_required' => 402,
    //     'forbidden' => 403,
    //     'not_found' => 404,
    //     'method_not_allowed' => 405,
    //     'not_acceptable' => 406,
    //     'proxy_authentication_required' => 407,
    //     'request_timeout' => 408,
    //     'conflict' => 409,
    //     'gone' => 410,
    //     'length_required' => 411,
    //     'precondition_failed' => 412,
    //     'request_entity_too_large' => 413,
    //     'request_URI_too_large' => 414,
    //     'unsupported_media_type' => 415,
    //     'request_range_not_satisfiable' => 416,
    //     'expectation_failed' => 417,
    //     'unprocessable_entity' => 422,
    //     'locked' => 423,
    //     'failed_dependency' => 424,
    //     'to_many_request'   =>  429,
    //     'internal_server_error' => 500,
    //     'not_implemented' => 501,
    //     'bad_gateway' => 502,
    //     'service_unavailable' => 503,
    //     'gateway_timeout' => 504,
    //     'insufficient_storage' => 507,
    // ];

    public function ValidateForm($fields, $rules)
    {
        Validator::make($fields, $rules)->validate();
    }

    public function getLangCodeFromField($field)
    {
        $lang_code = 'en';
        if (str_contains($field, '_')) {
            $position = strpos($field, '_');
            $lang_code = substr($field, 0, $position);
        }
        return $lang_code;
    }

    public function getColumnNameFromField($field)
    {
        $column = '';
        if (str_contains($field, '_')) {
            $position = strpos($field, '_') + 1;
            $column = substr($field, $position);
        }
        return $column;
    }

    public function getLangStoreData($request)
    {
        $actual_data = $data = $lang_codes = $columns = [];
        $default_lang_code = config('utility.default_lang_code');
        $language_alloweds = ['en', 'hi', 'ta', 'mr', 'bn', 'gu', 'kn', 'ml', 'or', 'pa', 'te', 'as'];

        foreach ($request->all() as $key => $req_data) {
            if ($req_data) {
                $lang_code = $this->getLangCodeFromField($key);

                if (!in_array($lang_code, $lang_codes)) {
                    array_push($lang_codes, $lang_code);
                }
                $column = $this->getColumnNameFromField($key);

                if ($column) {
                    if (!in_array($column, $columns)) {
                        array_push($columns, $column);
                    }
                    $data[$lang_code][$column] = $req_data;
                }
            }
        }

        if (count($data) > 0) {
            foreach ($language_alloweds as $language_allowed) {
                foreach ($columns as $column) {
                    if (array_key_exists($language_allowed, $data)) {
                        if (array_key_exists($column, $data[$language_allowed])) {
                            $actual_data[$language_allowed][$column] = $data[$language_allowed][$column];
                        } else {
                            $actual_data[$language_allowed][$column] = NULL;
                        }
                    } else {
                        $actual_data[$language_allowed] = $data[$default_lang_code];
                    }
                }
            }
        }
        return $actual_data;
    }

    // public function getLangStoreData($request){
    //     $actual_data = $data = $lang_codes = $columns = [];
    //     foreach($request->all() as $key => $req_data){
    //         if($req_data){
    //             $lang_code = $this->getLangCodeFromField($key);
    //             if($lang_code){
    //                 if(!in_array($lang_code,$lang_codes)){
    //                     array_push($lang_codes, $lang_code);
    //                 }
    //                 $column = $this->getColumnNameFromField($key);
    //                 if($column){
    //                     if(!in_array($column,$columns)){
    //                         array_push($columns, $column);
    //                     }
    //                     $data[$lang_code][$column] = $req_data; 
    //                 }
    //             }
    //         }
    //     }
    //     if(count($data) > 0){
    //         foreach($lang_codes as $lang_code){
    //             foreach($columns as $column){
    //                 if(array_key_exists($lang_code,$data)){
    //                     if(array_key_exists($column,$data[$lang_code])){
    //                         $actual_data[$lang_code][$column] = $data[$lang_code][$column];
    //                     }else{
    //                         $actual_data[$lang_code][$column] = null;
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     return $actual_data;
    // }

    public function DTFilters($request)
    {
        $filters = array(
            // 'draw' => $request['draw'],
            'offset' => isset($request['start']) ? $request['start'] : 0,
            'limit' => isset($request['length']) ? $request['length'] : 25,
            'sort_column' => (isset($request['order'][0]['column']) && isset($request['columns'][$request['order'][0]['column']]['data'])) ? $request['columns'][$request['order'][0]['column']]['data'] : 'created_at',
            'sort_order' => isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC',
            'search' => isset($request['search']['value']) ? $request['search']['value'] : '',
        );
        return $filters;
    }

    // APIs Validations
    public function apiValidator($fields, $rules, $version = "v.0.0", $message = array())
    {
        $validator = Validator::make($fields, $rules, $message);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $r_message  = '';
            $i = 1;
            foreach ($errors->messages() as $key => $message) {
                if ($i == 1) {
                    $r_message = $message[0];
                } else {
                    break;
                }
                $i++;
            }

            $is_subscribed = false;
            $subscription_end_date = "";
            if (!Auth::guest()) {
                if (Auth::user()->is_subscribed == 'y' && Auth::user()->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d')) {
                    $is_subscribed = true;
                } elseif (Auth::user()->gender == 'Female') {
                    $is_subscribed = true;
                }
                $subscription_end_date = Auth::user()->subscription_end_date;
            }

            // store api request and responce
            $apilogs = new ApiLogs();
            $apilogs->user_id = 0;
            $apilogs->url = url()->current();
            $apilogs->request = json_encode($fields);
            $apilogs->response = $r_message;
            $apilogs->api_status = $this->status;
            $apilogs->save();

            $this->response['meta']['message'] = $r_message;
            $this->response['meta']['url'] = url()->current();
            $this->response['meta']['language'] = app()->getLocale();
            $this->response['meta']['is_subscribed'] = $is_subscribed;
            $this->response['meta']['subscription_end_date'] = $subscription_end_date;
            $this->response['meta']['api'] = request()->route()->controller->getVersion();
            return false;
        }
        return true;
    }

    // Send JSON object as response
    public function returnResponse()
    {
        $is_subscribed = false;
        $subscription_end_date = "";
        if (!Auth::guest()) {
            $this->response['meta']['user_status'] = Auth::user()->user_status;
            if (Auth::user()->is_subscribed == 'y' && Auth::user()->subscription_end_date >= \Carbon\Carbon::today()->format('Y-m-d')) {
                $is_subscribed = true;
            } elseif (Auth::user()->gender == 'Female') {
                $is_subscribed = true;
            }
            $subscription_end_date = Auth::user()->subscription_end_date;
        }
        $this->response['meta']['url'] = url()->current();
        $this->response['meta']['api'] = request()->route()->controller->getVersion();
        $this->response['meta']['language'] = app()->getLocale();
        $this->response['meta']['is_subscribed'] = $is_subscribed;
        $this->response['meta']['subscription_end_date'] = $subscription_end_date;
        return response()->json($this->response, $this->status);
    }

    // Store Error Log
    public function storeErrorLog($error, $filename = 'laravel', $message = null)
    {
        if (empty($message)) {
            $message = trans('api.went_wrong');
        }
        $this->response['meta']['message'] = $message;

        // Add error log
        $iqTrackingLog = new Logger($filename);
        $iqTrackingLog->pushHandler(new StreamHandler(storage_path('logs/' . $filename . '.log')), Logger::ERROR);
        $iqTrackingLog->error($filename, ['error' => $error->getMessage()]);
    }

    // Custom Logs
    public function customLogger($data, $filename = 'laravel')
    {
        if (empty($data)) {
            $data = trans('api.went_wrong');
        }
        // Add error log
        $iqTrackingLog = new Logger($filename);
        $iqTrackingLog->pushHandler(new StreamHandler(storage_path('logs/' . $filename . '.log')), Logger::INFO);
        $iqTrackingLog->info(json_encode($data));
    }

    public function validateCheckSum($checksum, $contact)
    {
        $data = [
            'validate'  =>  false,
            'contact'   =>  $contact,
            'message'   =>  "Unable to process request!"
        ];
        try {
            $details = $this->decodeCheckSum($checksum)->details;
            if (!empty($details)) {
                $details = json_decode($details);
                if (!empty($details->contact_no) && !empty($details->time)) {
                    $requestTime = \Carbon\Carbon::parse($details->time);
                    if ($contact == $details->contact_no && $requestTime->addMinutes(config('utility.checksum.timelimit')) >= \Carbon\Carbon::now()) {
                        $data = [
                            'validate'  =>  true,
                            'contact'   =>  $contact,
                            'message'   =>  'Contact validated successfully!'
                        ];
                        return (object) $data;
                    }
                    throw new \App\Http\Controllers\Exceptions\InvalidCheckSum('485-412', $requestTime);
                }
                throw new \App\Http\Controllers\Exceptions\InvalidCheckSum('485-500');
            }
            throw new \App\Http\Controllers\Exceptions\InvalidCheckSum('485-404');
            // Return FALSE
        } catch (\App\Http\Controllers\Exceptions\InvalidCheckSum $exception) {
            $data = [
                'validate'  =>  false,
                'contact'   =>  $contact,
                'message'   =>  $exception->getMessage()
            ];
            return (object)$data;
        }
    }

    public function decodeCheckSum($checksum)
    {
        $key = config('utility.checksum.key');
        $algorithm = config('utility.checksum.algorithm');
        $pData = json_decode(base64_decode($checksum));

        $details = NULL;
        if (!empty($pData)) {
            $details = openssl_decrypt(
                $pData->value,
                $algorithm,
                $key,
                0,
                base64_decode($pData->iv)
            );
        }
        return (object) [
            'details' => $details
        ];
    }
}
