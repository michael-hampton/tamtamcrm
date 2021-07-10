<?php

namespace App\Requests\PlanSubscriptions;

use App\Repositories\Base\BaseFormRequest;

class UpdatePlanSubscription extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->plan_subscription);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'    => 'required',
            'plan_id' => 'required'
        ];
    }

}
