<?php

namespace App\Requests\TaxRate;

use App\Repositories\Base\BaseFormRequest;

class UpdateTaxRateRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->tax_rate);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'unique:tax_rates,name,' . $this->tax_rate->id . ',id,account_id,' . $this->account_id,
            'rate' => ['required']
        ];
    }
}
