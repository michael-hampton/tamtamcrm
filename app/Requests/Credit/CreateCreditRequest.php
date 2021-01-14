<?php

namespace App\Requests\Credit;

use App\Models\Credit;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreateCreditRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Credit::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id'    => 'required|exists:customers,id,account_id,' . auth()->user()->account_user()->account_id,
            'date'           => 'required',
            'due_date'       => 'required',
            'discount_total' => 'required',
            'sub_total'      => 'required',
            'total'          => 'required',
            'tax_total'      => 'required',
            'line_items'     => 'required|array',
            //'number'         => 'nullable|unique:invoices,number,customer,' . $this->customer_id,
            'number'         => [
                Rule::unique('credits', 'number')->where(
                    function ($query) {
                        return $query->where('customer_id', $this->customer_id)->where('account_id', $this->account_id);
                    }
                )
            ],
        ];
    }
}
