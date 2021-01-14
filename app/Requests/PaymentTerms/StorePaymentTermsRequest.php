<?php

namespace App\Requests\PaymentTerms;

use App\Models\PaymentTerms;
use App\Repositories\Base\BaseFormRequest;

class StorePaymentTermsRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', PaymentTerms::class);
    }

    public function rules()
    {
        return [
            'name'           => 'required',
            'number_of_days' => 'required',
        ];
    }
}
