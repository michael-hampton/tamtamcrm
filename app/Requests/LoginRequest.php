<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'    => 'required|email',
            // make sure the email is an actual email
            'password' => 'required|alphaNum|min:3'
            // password can only be alphanumeric and has to be greater than 3 characters
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'    => 'You must enter your email address!',
            'password.required' => 'You must enter a password! Please ensure that it is alphanumeric and greater than 3 characters',
        ];
    }
}
