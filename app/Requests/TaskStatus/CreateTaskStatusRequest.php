<?php

namespace App\Requests\TaskStatus;

use App\Models\TaskStatus;
use App\Repositories\Base\BaseFormRequest;

class CreateTaskStatusRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', TaskStatus::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:task_statuses'],
        ];
    }
}
