<?php

namespace App\Requests\TaxRate;

use App\Models\TaxRate;
use App\Repositories\Base\BaseFormRequest;

class CreateTaxRateRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', TaxRate::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:tax_rates,name,null,null,account_id,' .
                auth()->user()->account_user()->account_id,
            'rate' => 'required|numeric',
        ];
    }
}
