<?php

namespace App\Requests\Task;

use App\Models\Task;
use App\Repositories\Base\BaseFormRequest;

class UpdateTaskRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $task = Task::find($this->task_id);
        return auth()->user()->can('update', $task);
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
            //'content'   => 'required',
            //'contributors' => 'required|array',
            'due_date'    => 'required',
            'number'      => 'nullable|unique:tasks,number,' . $this->task_id . ',id,account_id,' . $this->account_id,
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
            'title.required'        => 'Title is required!',
            'description.required'  => 'Content is required!',
            'contributors.required' => 'Contributors is required!',
            'due_date.required'     => 'Due date is required!',
        ];
    }

}
