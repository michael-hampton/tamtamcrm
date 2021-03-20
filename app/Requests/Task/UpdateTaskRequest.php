<?php

namespace App\Requests\Task;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->task);
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
            'number'      => [
                'nullable',
                Rule::unique('tasks')->where(
                    function ($query) {
                        return $query->where('account_id', $this->task->account_id);
                    }
                )->ignore($this->task),
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
            'title.required'        => 'Title is required!',
            'description.required'  => 'Content is required!',
            'contributors.required' => 'Contributors is required!',
            'due_date.required'     => 'Due date is required!',
        ];
    }

}
