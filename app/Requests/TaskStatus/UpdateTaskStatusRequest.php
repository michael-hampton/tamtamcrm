<?php

namespace App\Requests\TaskStatus;

use App\Models\TaskStatus;
use App\Repositories\Base\BaseFormRequest;

class UpdateTaskStatusRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $task_status = TaskStatus::find($this->id);
        return auth()->user()->can('update', $task_status);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required']
        ];
    }

}
