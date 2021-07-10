<?php

namespace App\Requests\Quote;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateQuoteRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->quote);
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
                Rule::unique('quotes')->where(
                    function ($query) {
                        return $query->where('account_id', $this->quote->account_id);
                    }
                )->ignore($this->quote),
            ],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'comment.required' => 'You must enter a comment!',
            'task_id.required' => 'There was an unexpected error!',
            'user_id.required' => 'There was an unexpected error!',
        ];
    }
}
