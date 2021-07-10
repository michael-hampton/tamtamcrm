<?php

namespace App\Requests\Lead;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->lead);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => [
                'required',
                Rule::unique('leads')->ignore($this->lead->email)
            ],
            'name'       => 'required',
            'start_date' => 'nullable',
            //'task_status' => 'required',

        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        if (empty($input['industry_id'])) {
            $input['industry_id'] = null;
        }

        $this->replace($input);
    }

}
