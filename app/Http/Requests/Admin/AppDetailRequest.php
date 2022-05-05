<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AppDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'verification_image_male'       =>  'nullable|mimes:jpg,jpeg,png',
            'verification_image_female'     =>  'nullable|mimes:jpg,jpeg,png',
            'verification_video_male'       =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',
            'verification_video_female'     =>  'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm,flv,m3u8,ts,3gp,mov,avi,wmv,m4v',
        ];
    }
}
