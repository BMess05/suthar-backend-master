<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GiftRequest extends FormRequest
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
        if($this->id == null) {
            return [
                'name' => 'required|string|min:3|max:50',
                'photo' => 'required|image|mimes:jpg,jpeg,png,bmp,tiff|min:2|max:2048', // |dimensions:min_width=80,min_height=80,max_width=600,max_height=600
                'points' =>'required|numeric|min:1|max:9999999999',
                'stores' => 'required|array'
            ];
        } else {
            return [
                'name' => 'required|string|min:3|max:50',
                'photo' => 'sometimes|image|mimes:jpg,jpeg,png,bmp,tiff|min:2|max:2048', // |dimensions:min_width=80,min_height=80,max_width=600,max_height=600
                'points' =>'required|numeric|min:1|max:9999999999',
                'stores' => 'required|array'
            ];
        }
    }

    public function messages() { // static
        return [
            'points.max' => 'You cannot add more than 10 digits.'
        ];
    }
}
