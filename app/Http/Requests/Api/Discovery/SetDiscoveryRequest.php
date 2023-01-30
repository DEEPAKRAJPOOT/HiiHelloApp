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
            'Male',
            'Female',
            'Both',
            'দুয়োটা',
            'পুৰুষ',
            'মাইকী',
            'উভয়',
            'পুরুষ',
            'মহিলা',
            'બંને',
            'પુરુષ',
            'સ્ત્રી',
            'दोनों',
            'पुरुष',
            'महिला',
            'ಎರಡೂ',
            'ಪುರುಷ',
            'ಣ್ಣು',
            'രണ്ടും',
            'ആൺ',
            'സ്ത്രീ',
            'दोन्ही',
            'पुरुष',
            'स्त्री',
            'ଉଭୟ',
            'ପୁରୁଷ',
            'ମହିଳା',
            'ਦੋਵੇਂ',
            'ਨਰ',
            'ਔਰਤ',
            'இரண்டும்',
            'ஆண்',
            'பெண்',
            'రెండు',
            'పురుషుడు',
            'స్త్రీ'
        ];
        return [
            'distance'          =>  'required|numeric|min:1|max:500',
            'start_age'         =>  'required|numeric|min:1|max:100',
            'end_age'           =>  'required_with:start_age|numeric|min:1|max:100',
            'interest'          =>  'required|in:' . join(',', $interest_trans_arr),
            'location'          =>  'required|max:100',
            'languages'         =>  'nullable|array',
            'languages.*'       =>  'required',
        ];
    }
}
