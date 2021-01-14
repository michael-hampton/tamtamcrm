<?php

namespace App\Requests;

use App\Repositories\Base\BaseFormRequest;

class CreateMessageRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'     => 'required|numeric',
            'message'     => 'required|string',
            'customer_id' => 'required|numeric',
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
            'user_id.required'     => 'User is required!',
            'message.required'     => 'Message is required!',
            'customer_id.required' => 'Customer is required!',
        ];
    }

}
