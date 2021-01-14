<?php

namespace App\Requests\Credit;

use App\Models\Credit;
use App\Repositories\Base\BaseFormRequest;

class UpdateCreditRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $credit = Credit::find($this->credit_id);
        return auth()->user()->can('update', $credit);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id'    => 'required',
            'date'           => 'required',
            'due_date'       => 'required',
            'discount_total' => 'required',
            'sub_total'      => 'required',
            'total'          => 'required',
            'tax_total'      => 'required',
            'line_items'     => 'required|array',
            'number'         => 'nullable|unique:credits,number,' . $this->credit_id . ',id,account_id,' . $this->account_id,
        ];
    }
}
