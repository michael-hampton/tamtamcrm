<?php

namespace App\Requests\Credit;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateCreditRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->credit);
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
            'number'         => [
                'nullable',
                Rule::unique('credits')->where(
                    function ($query) {
                        return $query->where('account_id', $this->credit->account_id);
                    }
                )->ignore($this->credit),
            ],
        ];
    }
}
