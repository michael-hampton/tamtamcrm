<?php

namespace App\Requests\Project;

use App\Models\Project;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreateProjectRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Project::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|unique:projects,name,null,null,account_id,' . auth()->user()->account_user(
                )->account_id,
            'description' => 'string|required',
            'customer_id' => 'required|exists:customers,id,account_id,' . auth()->user()->account_user()->account_id,
            'company_id'  => 'bail|nullable|sometimes|exists:companies,id,account_id,' . auth()->user()->account_user(
                )->account_id,
            'number'      => [
                Rule::unique('projects', 'number')->where(
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
            'title.required'       => 'Title is required!',
            'description.required' => 'Description is required!',
            'customer_id.required' => 'Customer is required!'
        ];
    }

}
