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
            'পুৰুষ' => 'Female',
            'মাইকী' => 'Male',

            'উভয়' => 'Both',
            'পুরুষ' => 'Female',
            'মহিলা' => 'Male',

            'બંને' => 'Both',
            'પુરુષ' => 'Female',
            'સ્ત્રી' => 'Male',

            'दोनों' => 'Both',
            'पुरुष' => 'Female',
            'महिला' => 'Male',

            'ಎರಡೂ' => 'Both',
            'ಪುರುಷ' => 'Female',
            'ಣ್ಣು' => 'Male',

            'രണ്ടും' => 'Both',
            'ആൺ' => 'Female',
            'സ്ത്രീ' => 'Male',

            'दोन्ही' => 'Both',
            'पुरुष' => 'Female',
            'स्त्री' => 'Male',

            'ଉଭୟ' => 'Both',
            'ପୁରୁଷ' => 'Female',
            'ମହିଳା' => 'Male',

            'ਦੋਵੇਂ' => 'Both',
            'ਨਰ' => 'Female',
            'ਔਰਤ' => 'Male',

            'இரண்டும்' => 'Both',
            'ஆண்' => 'Female',
            'பெண்' => 'Male',

            'రెండు' => 'Both',
            'పురుషుడు' => 'Female',
            'స్త్రీ' => 'Male'
        ];
        return [
            'distance'          =>  'required|numeric|min:1|max:500',
            'start_age'         =>  'required|numeric|min:1|max:100',
            'end_age'           =>  'required_with:start_age|numeric|min:1|max:100',
            'interest'          =>  'required|in:' . join(',', array_keys($interest_trans_arr)),
            'location'          =>  'required|max:100',
            'languages'         =>  'nullable|array',
            'languages.*'       =>  'required',
        ];
    }
}
