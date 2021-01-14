<?php

namespace App\Requests\Subscription;

use App\Models\Subscription;
use App\Repositories\Base\BaseFormRequest;

class CreateSubscriptionRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Subscription::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'target_url' => 'required|url',
            'event_id'   => 'required',
        ];
    }
}
