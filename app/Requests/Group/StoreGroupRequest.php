<?php

namespace App\Requests\Group;

use App\Models\Group;
use App\Repositories\Base\BaseFormRequest;

class StoreGroupRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Group::class);
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:groups,name,null,null,account_id,' . auth()->user()->account_user()->account_id
        ];
    }
}
