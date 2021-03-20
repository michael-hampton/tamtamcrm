<?php

namespace App\Requests\Expense;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->expense);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'amount' => 'required',
            'number' => [
                'nullable',
                Rule::unique('expenses')->where(
                    function ($query) {
                        return $query->where('account_id', $this->expense->account_id);
                    }
                )->ignore($this->expense),
            ],
        ];

        return $rules;
    }

}
