<?php

namespace App\Requests\PaymentTerms;

use App\Models\PaymentTerms;
use App\Repositories\Base\BaseFormRequest;

class UpdatePaymentTermsRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->payment_term);
    }

    public function rules()
    {
        return [
            'name'           => 'required',
            'number_of_days' => 'required',
        ];
    }
}
