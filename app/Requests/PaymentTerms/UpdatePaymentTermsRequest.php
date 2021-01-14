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
        $payment_term = PaymentTerms::find(request()->segment(3));
        return auth()->user()->can('update', $payment_term);
    }

    public function rules()
    {
        return [
            'name'           => 'required',
            'number_of_days' => 'required',
        ];
    }
}
