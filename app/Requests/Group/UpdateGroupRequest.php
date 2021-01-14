<?php

namespace App\Requests\Group;

use App\Models\Group;
use App\Repositories\Base\BaseFormRequest;

class UpdateGroupRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $group = Group::find($this->group_id);
        return auth()->user()->can('update', $group);
    }

    public function rules()
    {
        return [
            'name' => 'unique:groups,name,' . $this->group_id . ',id,account_id,' . $this->account_id
        ];
    }
}
