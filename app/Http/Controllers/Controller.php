<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

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
                ],
            ];

    public $status = 412;
    public $statusArr = [
        'success'=> 200,
        'bad_request' => 400,
        'authorization_required' => 401,
        'payment_required' => 402,
        'forbidden' => 403,
        'not_found' => 404,
        'method_not_allowed' => 405,
        'not_acceptable' => 406,
        'proxy_authentication_required' => 407,
        'request_timeout' => 408,
        'conflict' => 409,
        'gone' => 410,
        'length_required' => 411,
        'precondition_failed' => 412,
        'request_entity_too_large' => 413,
        'request_URI_too_large' => 414,
        'unsupported_media_type' => 415,
        'request_range_not_satisfiable' => 416,
        'expectation_failed' => 417,
        'unprocessable_entity' => 422,
        'locked' => 423,
        'failed_dependency' => 424,
        'to_many_request'   =>  429,
        'internal_server_error' => 500,
        'not_implemented' => 501,
        'bad_gateway' => 502,
        'service_unavailable' => 503,
        'gateway_timeout' => 504,
        'insufficient_storage' => 507,
    ];

    public function ValidateForm($fields, $rules)
    {
        Validator::make($fields, $rules)->validate();
    }

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

            $this->response['meta']['message'] = $r_message;
            $this->response['meta']['url'] = url()->current();
            $this->response['meta']['language'] = app()->getLocale();
            $this->response['meta']['api'] = request()->route()->controller->getVersion();
            return false;
        }
        return true;
    }

    // Send JSON object as response
    public function return_response()
    {
        $this->response['meta']['language'] = app()->getLocale();
        return response()->json($this->response, $this->status);
    }
}
