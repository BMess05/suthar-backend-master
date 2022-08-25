<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                'email' => 'required|email|unique:users,email', // email::rfc,dns
                'phone_number' => 'required|digits:10',
                'stores' => 'required|array'
            ];
        }   else {
            return [
                'name' => 'required|min:3|max:30',
                'email' => 'required|email|unique:users,email,'.$this->id, // email::rfc,dns
                'phone_number' => 'required|digits:10',
                'stores' => 'required|array'
            ];
        }


    }
}
