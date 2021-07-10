<?php

namespace App\Requests\TwoFactor;

use App\Repositories\Base\BaseFormRequest;

class TwoFactorVerification extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'secret'            => 'required',
            'one_time_password' => 'required',
        ];
    }
}
