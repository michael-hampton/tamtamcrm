<?php

namespace App\Requests\Plan;

use App\Models\Plan;
use App\Repositories\Base\BaseFormRequest;

class CreatePlanRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Plan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:plans,name,null,null,account_id,' .
                auth()->user()->account_user()->account_id,
            'code' => 'required'
        ];
    }
}
