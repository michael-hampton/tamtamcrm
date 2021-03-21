<?php

namespace App\Requests\Expense;

use App\Models\Expense;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreateExpenseRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Expense::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'date'                => 'required',
            'expense_category_id' => 'required',
            'amount'              => 'required',
            'number'      => [
                'nullable',
                Rule::unique('expenses', 'number')->where(
                    function ($query) {
                        return $query->where('customer_id', $this->customer_id)->where('account_id', auth()->user()->account_user()->account_id);
                    }
                )
            ],
        ];

        return $rules;
    }

}
