<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\ { Request, Response };
use Illuminate\Database\Eloquent\ { ModelNotFoundException };
use Illuminate\Support\Facades\ { Storage };
use App\Http\Requests\Api\User\ { UploadVerifyDetailRequest, EmailVerifyRequest };
use App\Http\Resources\v1\ { VerificationResource };

class VerificationController extends Controller
{
    private $version = "v.1.0";
    public function getVersion(){ return $this->version; }

    // Upload Verification Details
    public function uploadVerifyDetail(Request $request)
    {
        $rules = UploadVerifyDetailRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $path = NULL;
                $user = $request->user();

                if($request->type == 'image'){
                    if( Storage::exists($user->verify_photo) ) { Storage::delete($user->verify_photo); }

                    $path = $request->file('file')->store('users/verify/image');
                    $user->verify_photo = $path;
                    $user->photo_verified_at = NULL;
                }
                elseif($request->type == 'video'){
                    if( Storage::exists($user->verify_video) ) { Storage::delete($user->verify_video); }

                    $path = $request->file('file')->store('users/verify/video');
                    $user->verify_video = $path;
                    $user->video_verified_at = NULL;
                }
                $user->verify_status = 'under_review';
                $user->save();

                if($path){
                    $this->status = Response::HTTP_OK;
                    return (['data'  =>  NULL,
                            'meta' => [
                                'url'       =>  url()->current(),
                                'api'       =>  $this->getVersion(),
                                'language'  =>  app()->getLocale(),
                                'message'   =>  trans('api.verification_upload.success'),
                            ] ]);
                }else{
                    $this->response['meta']['message']  =   trans('api.verification_upload.fail');
                    $this->status = Response::HTTP_NOT_FOUND; 
                }
            } catch(ModelNotFoundException $exception) {                
                switch ($exception->getModel()) {
                    case 'App\Models\User':
                        $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("User")]);
                        break;
                    default:
                        $this->response['meta']['message'] = trans('api.went_wrong');
                        break;
                };
            } catch (\Exception $e) {
                $this->storeErrorLog($e,'upload_verify_detail');
            }
        }
        return $this->returnResponse();
    }

    // Verify Details
    public function verifyEmail(Request $request)
    {
        $rules = EmailVerifyRequest::rules();
        if( $this->apiValidator($request->all(), $rules) ) {
            try{
                $user = $request->user();
                if(!empty($user->email) && $user->email != $request->email){
                    $this->response['meta']['message']  =   trans('api.invalid', ['entity' => __("email")]);
                    $this->status = Response::HTTP_NOT_FOUND; 
                    return $this->returnResponse();
                }
                // else{
                //     $user->email = $request->email; $user->save();
                // }

                /* Send Verification */    
                $user->sendEmailVerificationNotification();
                return ([
                    'data'  =>  NULL,
                    'meta' => [
                        'url'       =>  url()->current(),
                        'api'       =>  $this->getVersion(),
                        'language'  =>  app()->getLocale(),
                        'message'   =>  trans('api.link_sent', ['entity' => __('Verification email')]),
                ] ]);

            } catch (\Exception $e) {
                $this->response['meta']['message'] = trans('api.link_not_send');
                $this->storeErrorLog($e,'verify_email');
            }
        }
        return $this->returnResponse();
    }

    /* Get Verification Details */
    public function getVerifyDetails(Request $request)
    {
        try{
            $user = $request->user();
            return (new VerificationResource($user))->additional([
                'meta'  =>  [
                    'message'   =>  trans('api.list', ['entity' =>  __('Verification details')]),
                ]
            ]);
        } catch (\Exception $e) {
            $this->response['meta']['message'] = trans('api.link_not_send');
            $this->storeErrorLog($e,'get_verify_detail');
        }
        return $this->returnResponse();
    }
}
