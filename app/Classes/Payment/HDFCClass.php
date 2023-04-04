<?php

namespace App\Classes\Payment;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class HDFCClass
{
    private $base_url;
    private $end_points = [
        "payment_intent" => "hupi/mePayInetentReq",
        "payment_status" => "hupi/transactionStatusQuery",
        "refund_intent" => "hupi/refundReqSvc"
    ];

    private $trans_status = [
        'SUCCESS' => 'success',
        'FAILED' => 'fail',
        'FAILURE' => 'fail',
        'PENDING' => 'pending',
        'EXPIRED' => 'fail',
        'REJECTED' => 'fail',
        'SPAM' => 'fail',
    ];

    private $cb_response_body = [
        "upi_transaction_id" => null,
        "merchant_transaction_id" => null,
        "amount" => null,
        "timestamp" => null,
        "status" => null,
        "status_description" => null,
        "response_code" => null,
        "approval_number" => null,
        "payer_virtual_address" => null,
        "customer_reference_no" => null,
        "reference_id" => null,
        "AF1" => null,
        "AF2" => null,
        "AF3" => null,
        "AF4" => null,
        "AF5" => null,
        "AF6" => null,
        "AF7" => null,
        "AF8" => null,
        "AF9" => "NA",
        "AF10" => "NA",
    ];

    private $secure_key = null;
    private $merchant_id = null;
    private $merchant_name = null;
    private $merchant_vpa = null;
    private $merchant_mcc = null;
    private $currency = null;
    private $isProd = false;

    public function  __construct()
    {
        $env = config('app.env');
        $this->isProd = $env === 'production';
        if ($this->isProd) {
            $this->end_points = [
                "payment_intent" => "upi/mePayInetentReq",
                "payment_status" => "upi/transactionStatusQuery",
                "refund_intent" => "upi/refundReqSvc"
            ];
        }

        $this->base_url = config('utility.hdfc.end_point');
        $this->secure_key = config('utility.hdfc.secure_key');
        $this->merchant_id = config('utility.hdfc.merchant_id');
        $this->merchant_name = config('utility.hdfc.merchant_name');
        $this->merchant_vpa = config('utility.hdfc.merchant_vpa');
        $this->merchant_mcc = config('utility.hdfc.merchant_mcc');
        $this->currency = config('utility.hdfc.currency');
    }

    public function createTransactionRequest($data)
    {
        $request_data = [
            "PGMerchantId" => $this->merchant_id,
            "TransactionId" => "",
            "MerchantCategoryCode" => $this->merchant_mcc,
            "PayType" => "P2M",
            "TransactionType" => "Pay",
            "TransactionDescription" => "",
            "PayeeVirtualId" => $this->merchant_vpa,
            "PayeeName" => $this->merchant_name,
            "Amount" => "",
            "AF1" => "",
            "AF2" => "",
            "AF3" => "",
            "AF4" => "",
            "AF5" => "",
            "AF6" => "",
            "AF7" => "",
            "AF8" => "",
            "AF9" => "NA",
            "AF10" => "NA",
        ];
        $request_data["TransactionId"] = $data['transaction_id'];
        $request_data["TransactionDescription"] = $data['description'];
        $request_data['Amount'] = number_format($data["amount"], 2);
        $request_data["AF1"] = $data['user_id'];
        $request_data["AF2"] = $data['plan_custom_id'];

        $body = [
            'pgMerchantId' => $this->merchant_id,
            "requestMsg" => $this->encrypt(join("|", $request_data))
        ];
        $pRes = $this->postRequest('payment_intent', $body);
        $request_data['request'] = $body;
        $request_data['response'] = $pRes;
        return $request_data;
    }

    public function getTransactionStatus($data)
    {
        $request_data = [
            "PGMerchantId" => $this->merchant_id,
            "TransactionId" => $data['transaction_id'],
            "UPITxnID" => "",
            "customer_refno" => "",
            "AF1" => "",
            "AF2" => "",
            "AF3" => "",
            "AF4" => "",
            "AF5" => "",
            "AF6" => "",
            "AF7" => "",
            "AF8" => "",
            "AF9" => "NA",
            "AF10" => "NA",
        ];

        $body = [
            'pgMerchantId' => $this->merchant_id,
            "requestMsg" => $this->encrypt(join("|", $request_data))
        ];
        $response_data = $this->postRequest('payment_status', $body);
        $decrypted_str = $this->decrypt($response_data);

        return [
            'mapped_request' => $request_data,
            'raw_request' => $body,
            'raw_response' => $response_data,
            'mapped_response' => $this->mapCallbackResponse($decrypted_str)
        ];
    }

    public function createRefundRequest($data){
        $request_data = [
            'pg_merchant_id'              => $this->merchant_id,
            'new_transaction_id'          => 'REF-'.substr($data['transaction_id'],-15).'-'.time(),
            'original_transaction_id'     => $data['transaction_id'],
            'original_transaction_ref_no' => '',
            'original_customer_refno'     => $data['customer_reference_no'],
            'remarks'                     => 'refund for user '.($data['user_id'] ?? 'unknown').' with plan '.($data['plan_id'] ?? 'unknown'),
            'refund_amount'               => number_format($data['amount'],2),
            'currency'                    => $this->currency,
            'transaction_type'            => 'P2P',
            'payment_type'                => 'PAY',
            'AF1'                         => '',
            'AF2'                         => '',
            'AF3'                         => '',
            'AF4'                         => '',
            'AF5'                         => '',
            'AF6'                         => '',
            'AF7'                         => '',
            'AF8'                         => '',
            'AF9'                         => 'NA',
            'AF10'                        => 'NA',
        ];
        $body = [
            'pgMerchantId' => $this->merchant_id,
            'requestMsg' => $this->encrypt(join('|',$request_data))
        ];
        $response_data = $this->postRequest('refund_intent', $body);
        $decrypted_str = $this->decrypt($response_data);
        return [
            'mapped_request' => $request_data,
            'raw_request' => $body,
            'raw_response' => $response_data,
            'mapped_response' => $this->mapCallbackResponse($decrypted_str)
        ];
    }

    public function parseHDFCCallback($key)
    {
        $decrypted_str = $this->decrypt($key);
        if (!$decrypted_str) {
            throw new Exception('Invalid string data');
        }
        return $this->mapCallbackResponse($decrypted_str);
    }

    private function mapCallbackResponse($decrypted_str)
    {
        $callBackBody = $this->cb_response_body;
        $arr_data = explode("|", $decrypted_str);
        $trans_arr =  array_combine(array_keys($callBackBody), $arr_data);
        return [
            'upi_transaction_id' => $trans_arr['upi_transaction_id'],
            'transaction_id' => $trans_arr['merchant_transaction_id'],
            'amount' => $trans_arr['amount'],
            'status' => $this->trans_status[$trans_arr['status']] ?? $trans_arr['status'],
            'status_description' => $trans_arr['status_description'] ?? '',
            'user_id' => $trans_arr['AF1'],
            'plan_id' => $trans_arr['AF2'],
            'customer_reference_no' => $trans_arr['customer_reference_no']
        ];
    }

    private function mapOriginalCallbackResponse($decrypted_str)
    {
        $callBackBody = $this->cb_response_body;
        $arr_data = explode('|',$decrypted_str);
        return array_combine(array_keys($callBackBody),$arr_data);
    }

    private function postRequest($end_point, $request_data)
    {

        $host_url = $this->base_url . $this->end_points[$end_point];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ]);
        if (!$this->isProd) {
            $response = $response->withoutVerifying()
                ->withOptions(["verify" => false]);
        }
        $response = $response->post($host_url, $request_data);
        if ($response->status() === 200) {
            return $response->body();
        }
        throw new Exception('Error in api');
    }

    private function encrypt($input)
    {
        $secure_key = hex2bin($this->secure_key);
        return bin2hex(openssl_encrypt($input, 'AES-128-ECB', $secure_key, OPENSSL_RAW_DATA));
    }

    private function decrypt($input)
    {
        $input = hex2bin($input);
        $secure_key = hex2bin($this->secure_key);
        return openssl_decrypt($input, 'AES-128-ECB', $secure_key, OPENSSL_RAW_DATA);
    }
}
