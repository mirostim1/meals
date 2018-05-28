<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchRequest extends FormRequest
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
            'lang'      => 'required|bail|alpha|min:2|max:2|in:en,hr,de',
            'category'  => array('regex:/(^null$|^!null$|^\d{1,2}$)/', 'bail', 'nullable'),
            'tags'      => array('regex:/^[0-9]{1,2}([,][0-9]{1,2})*$/', 'bail'),
            'diff_time' => 'integer|min:1|max:2147472000|bail', /** max: 19-01-2038 */
            'per_page'  => 'integer|min:1|max:60|bail',
            'page'      => 'integer|min:1|max:99|bail',
            'with'      => array('regex:/^[a-zA-Z\,]*$/', 'bail')
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['error' => $validator->errors()->first()], 422));
    }
}
