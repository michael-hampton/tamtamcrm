<?php

namespace App\Requests\Promocode;

use App\Repositories\Base\BaseFormRequest;

class UpdatePromocode extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'scope'       => ['required'],
            'scope_value' => ['required'],
            'amount'      => ['required'],
            'quantity'    => ['required'],
            'description' => ['required'],
            'expires_at'  => ['required']
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();
        $input['expires_at'] = date('Y-m-d', strtotime($input['expires_at']));

        $this->replace($input);
    }
}