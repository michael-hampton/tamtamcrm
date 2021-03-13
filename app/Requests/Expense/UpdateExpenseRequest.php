<?php

namespace App\Requests\Expense;

use App\Models\Expense;
use App\Repositories\Base\BaseFormRequest;

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
            'amount' => 'required'
        ];

        return $rules;
    }

}
