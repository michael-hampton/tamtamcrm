<?php

namespace App\Requests\Project;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'string|required',
            'description' => 'string|required',
            'customer_id' => 'numeric|required',
            'number'      => [
                'nullable',
                Rule::unique('projects')->where(
                    function ($query) {
                        return $query->where('account_id', $this->project->account_id);
                    }
                )->ignore($this->project),
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
            'title.required'       => 'Title is required!',
            'description.required' => 'Description is required!',
            'customer_id.required' => 'Customer is required!'
        ];
    }

}
