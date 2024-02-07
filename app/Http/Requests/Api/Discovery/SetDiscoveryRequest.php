<?php

namespace App\Http\Requests\Api\Discovery;

use Illuminate\Foundation\Http\FormRequest;

class SetDiscoveryRequest extends FormRequest
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
        $interest_trans_arr = [
            'Both' => 'Both',
            'Female' => 'Female',
            'Male' => 'Male',

            'দুয়োটা' => 'Both',
            'পুৰুষ' => 'Male',
            'মাইকী' => 'Female',

            'উভয়' => 'Both',
            'পুরুষ' => 'Male',
            'মহিলা' => 'Female',

            'બંને' => 'Both',
            'પુરુષ' => 'Male',
            'સ્ત્રી' => 'Female',

            'दोनों' => 'Both',
            'पुरुष' => 'Male',
            'महिला' => 'Female',

            'ಎರಡೂ' => 'Both',
            'ಪುರುಷ' => 'Male',
            'ಣ್ಣು' => 'Female',

            'രണ്ടും' => 'Both',
            'ആൺ' => 'Male',
            'സ്ത്രീ' => 'Female',

            'दोन्ही' => 'Both',
            'पुरुष' => 'Male',
            'स्त्री' => 'Female',

            'ଉଭୟ' => 'Both',
            'ପୁରୁଷ' => 'Male',
            'ମହିଳା' => 'Female',

            'ਦੋਵੇਂ' => 'Both',
            'ਨਰ' => 'Male',
            'ਔਰਤ' => 'Female',

            'இரண்டும்' => 'Both',
            'ஆண்' => 'Male',
            'பெண்' => 'Female',

            'రెండు' => 'Both',
            'పురుషుడు' => 'Male',
            'స్త్రీ' => 'Female'
        ];
        return [
            'distance'          =>  'required|numeric|min:1|max:500',
            'start_age'         =>  'required|numeric|min:1|max:100',
            'end_age'           =>  'required_with:start_age|numeric|min:1|max:100',
            'interest'          =>  'required|in:' . join(',', array_keys($interest_trans_arr)),
            // 'location'          =>  'required|max:100',
            'languages'         =>  'nullable|array',
            'languages.*'       =>  'required',
        ];
    }
}
