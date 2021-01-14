<?php

namespace App\Requests\Token;

use App\Repositories\Base\BaseFormRequest;

class CreateTokenRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
