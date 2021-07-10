<?php

namespace App\Requests\PlanSubscriptions;

use App\Models\PlanSubscription;
use App\Repositories\Base\BaseFormRequest;

class CreatePlanSubscription extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', PlanSubscription::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'    => 'required|unique:plan_subscriptions,name,null,null,account_id,' .
                auth()->user()->account_user()->account_id,
            'plan_id' => 'required'
        ];
    }
}
