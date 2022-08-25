<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractorRequest extends FormRequest
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
                'name' => 'required|min:3|max:30',
                'email' => 'required|email|unique:contractors,email', // email::rfc,dns
                'phone' => 'digits:10|nullable', // |digits:10
                'address' => 'sometimes', // |string|min:5|max:50'
                'type' => 'required|in:1,2',
                'store' => 'required|exists:stores,id'
            ];
        }   else {
            return [
                'name' => 'required|min:3|max:30',
                'email' => 'required|email|unique:contractors,email,'.$this->id, // email::rfc,dns
                'phone' => 'digits:10|nullable', // |digits:10
                'address' => 'sometimes', // |string|min:5|max:50'
                'type' => 'required|in:1,2',
                'store' => 'required|exists:stores,id'
            ];
        }
    }
}
