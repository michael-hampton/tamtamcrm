<?php

namespace App\Requests\Deal;

use App\Repositories\Base\BaseFormRequest;

class UpdateDealRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->deal);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'valued_at'   => 'nullable|string',
            'rating'      => 'nullable|numeric',
            'customer_id' => 'nullable|numeric',
            'name'        => 'required',

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
            'name.required'         => 'Name is required!',
            'content.required'      => 'Content is required!',
            'contributors.required' => 'Contributors is required!',
            'due_date.required'     => 'Due date is required!',
        ];
    }

}
