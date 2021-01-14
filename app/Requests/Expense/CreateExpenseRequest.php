<?php

namespace App\Requests\Expense;

use App\Models\Expense;
use App\Repositories\Base\BaseFormRequest;

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
            'amount'              => 'required'
        ];

        return $rules;
    }

}
