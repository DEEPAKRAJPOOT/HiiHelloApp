<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response};
use Illuminate\Database\Eloquent\{ModelNotFoundException};
use Illuminate\Support\Facades\{Storage};
use App\Http\Requests\Api\User\{UploadVerifyDetailRequest, EmailVerifyRequest, VerifyContactRequest};
use App\Http\Resources\v1\{VerificationResource};
use App\Models\{User, Country};
use App\Jobs\{NotificationJob};


use Aws\Rekognition\RekognitionClient;

class VerificationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Upload Verification Details
    public function uploadVerifyDetail(Request $request)
    {
        $uploadVerifyDetailRequest = new UploadVerifyDetailRequest();
        if ($this->apiValidator($request->all(), $uploadVerifyDetailRequest->rules())) {
            try {
                $path = NULL;
                $user = $request->user();
                $safe_image = "true";

                if ($request->type == 'image') {


                    if (Storage::exists($user->verify_photo)) {
                        Storage::delete($user->verify_photo);
                    }
                    //CHECK FOR AWS REKOGNIZTION START

                    ///CHECK FOR AWS REKOGNIZTION START
                    $awsImgResultArr = checkAwsImageModeration($request,"file");

                    if(count($awsImgResultArr) > 0)
                    {
                        if($awsImgResultArr["is_safe_image"]==true) 
                        {
                            $path = $request->file('file')->store('users/verify/image');
                            $user->verify_photo = $path;
                            $user->verify_photo_status = "under_review";
                            $user->photo_verified_at = NULL;
                        }   
                        else
                        {
                            $user->verify_photo = NULL;
                            $user->verify_photo_status = "unverified";
                            $user->photo_verified_at = NULL;
                            $safe_image = "false";                            
                        }  
                    }                      
                    //CHECK FOR AWS REKOGNIZTION END

                } elseif ($request->type == 'video') {
                    if (Storage::exists($user->verify_video)) {
                        Storage::delete($user->verify_video);
                    }

                    $path = $request->file('file')->store('users/verify/video');
                    $user->verify_video = $path;
                    $user->verify_video_status = "under_review";
                    $user->video_verified_at = NULL;
                }
                
                $user->verify_status = 'under_review';
                $user->save();

                if ($path) {
                    $this->status = Response::HTTP_OK;
                    return ([
                        'data'  =>  NULL,
                        'meta' => [
                            'url'       =>  url()->current(),
                            'api'       =>  $this->getVersion(),
                            'language'  =>  app()->getLocale(),
                            'is_ban'    =>  false,
                            'safe_image'    =>  $safe_image,     
                            'message'   =>  trans('api.verification_upload.success'),
                        ]
                    ]);
                } else {
                    $this->response['meta']['message']  =   trans('api.verification_upload.fail');
                    $this->response['meta']['is_ban'] = false;
                    $this->response['meta']['safe_image'] = $safe_image;                    
                    $this->status = Response::HTTP_NOT_FOUND;
                }
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'upload_verify_detail');
            }
        }
        return $this->returnResponse();
    }

    // Upload Verification Details
    public function verifyContactNumber(Request $request)
    {
        $verifyContactRequest = new VerifyContactRequest();
        if ($this->apiValidator($request->all(), $verifyContactRequest->rules())) {
            try {
                $country = Country::select('phonecode')->wherePhonecode($request->country_code)
                    ->whereIsActive('y')->firstOrFail();

                $user = $request->user();
                $user->country_code = $country->phonecode;
                $user->contact_no = $request->contact_no;
                $user->contact_verified_at  = \Carbon\Carbon::now();
                $user->save();

                $this->status = Response::HTTP_OK;
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.verification.success', ['entity' => __("Contact number")]),
                    ]
                ]);
            } catch (ModelNotFoundException $exception) {
                switch ($exception->getModel()) {
                    case 'App\Models\Country':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Country")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        $this->response['meta']['is_ban'] = false;
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        $this->response['meta']['is_ban'] = false;
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e, 'verify_contact_number');
            }
        }
        return $this->returnResponse();
    }

    // Verify Details
    public function verifyEmail(Request $request)
    {
        $emailVerifyRequest = new EmailVerifyRequest();
        if ($this->apiValidator($request->all(), $emailVerifyRequest->rules())) {
            $user = $request->user();
            try {

                if(empty($request->email))
                {
                    if (empty($user->email))
                    {                        
                        $this->response['meta']['message']  =   trans('api.not_found', ['entity' => __("email")]);
                        $this->response['meta']['is_ban'] = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                        return $this->returnResponse();
                    }                       
                }
                else
                {
                    if (!empty($user->email) && $user->email != $request->email) {
                        $this->response['meta']['message']  =   trans('api.invalid', ['entity' => __("email")]);
                        $this->response['meta']['is_ban'] = false;
                        $this->status = Response::HTTP_NOT_FOUND;
                        return $this->returnResponse();
                    } else {
                        $email_exist = User::select('id')->whereEmail($request->email)->first();
                        if (!$email_exist) {
                            $user->email = $request->email;
                            $user->save();
                        } else {
                            $this->response['meta']['message']  =   trans('api.already_exists', ['entity' => __("email")]);
                            $this->response['meta']['is_ban'] = false;
                            $this->status = Response::HTTP_NOT_FOUND;
                            return $this->returnResponse();
                        }
                    }
                }    

                /* Send Verification */
                $user->sendEmailVerificationNotification();
                $user->verify_email_send = 'y';
                $user->verify_status = 'under_review';
                $user->save();

                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'is_ban'    =>  false,
                        'message'   =>  trans('api.link_sent', ['entity' => __('Verification email')]),
                    ]
                ]);
            } catch (\Exception $e) {
                $notification = [
                    'custom_id'     =>  getUniqueString('notifications'),
                    'key'           =>  'user_id',
                    'value'         =>  $user->id,
                    'user_id'       =>  $user->id,
                    'title'         =>  trans('api.notify_message.verify_fail_email.title'),
                    'message'       =>  trans('api.notify_message.verify_fail_email.message'),
                    'image'         =>  '',
                    'type'          =>  config('utility.notification.type.verify_fail_email'),
                ];

                // Notify
               /* $notificationJob = new NotificationJob($notification, $user);
                dispatch($notificationJob);*/

                $this->response['meta']['message'] = trans('api.link_not_send');
                $this->storeErrorLog($e, 'verify_email');
            }
        }
        return $this->returnResponse();
    }

    /* Get Verification Details */
    public function getVerifyDetails(Request $request)
    {
        try {
            $user = $request->user();
            return (new VerificationResource($user))->additional([
                'meta'  =>  [
                    'message'   =>  trans('api.list', ['entity' =>  __('Verification details')]),
                    'is_ban'    =>  false,
                ]
            ]);
        } catch (\Exception $e) {
            $this->response['meta']['message'] = trans('api.link_not_send');
            $this->response['meta']['is_ban'] = false;
            $this->storeErrorLog($e, 'get_verify_detail');
        }
        return $this->returnResponse();
    }
}
