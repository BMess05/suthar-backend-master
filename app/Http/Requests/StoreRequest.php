<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        // $cities = config('constants.cities');
        return [
            'name' => 'required|string|min:3|max:30',
            'city' => 'required'
        ];
    }

    public function messages() { // static
        return [
            'name.max' => 'The maximum character for the name field must be 30.'
        ];
    }
}
