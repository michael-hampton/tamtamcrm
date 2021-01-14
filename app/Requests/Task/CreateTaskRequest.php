<?php

namespace App\Requests\Task;

use App\Models\Task;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Task::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_type' => 'nullable|numeric',
            'rating'      => 'nullable|numeric',
            'customer_id' => 'nullable|numeric',
            'name'        => 'required',
            'description' => 'required',
            //'contributors' => 'required|array',
            'due_date'    => 'required',
            'start_date'  => 'nullable',
            'project_id'  => 'nullable',
            'number'      => [
                Rule::unique('tasks', 'number')->where(
                    function ($query) {
                        return $query->where('customer_id', $this->customer_id)->where('account_id', $this->account_id);
                    }
                )
            ],
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
            'title.required'    => 'Title is required!',
            'content.required'  => 'Content is required!',
            //'contributors.required' => 'Contributors is required!',
            'due_date.required' => 'Due date is required!',
        ];
    }

}
