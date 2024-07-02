<?php

namespace App\Http\Controllers\api\v1;

use App\Mail\QueryMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\QueryToSupportJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Query\QueryRequest;

class QueryController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    public function submitUserQuery(Request $request)
    {
        $queryRequest = new QueryRequest();
        if (!$this->apiValidator($request->all(), $queryRequest->rules())) {
            return $this->returnResponse();
        }

        $data = $request->except('file');
        $data['file'] = null;
        if ($request->hasFile('file')) {
            $data['file'] = generateURL($request->file('file')->store('support/files'));
        }
        
        //dispatch(new QueryToSupportJob($data));
        Mail::to("appsupport@hihelloapp.com")->send(new QueryMail($data));
        $this->response['meta']['message']  =   trans('api.add', ['entity' => __('Query')]);
        $this->response['meta']['is_ban'] = false;
        $this->status = Response::HTTP_OK;
        return $this->returnResponse();
    }
}
