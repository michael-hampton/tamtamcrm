<?php

namespace App\Requests\CaseTemplate;


use App\Repositories\Base\BaseFormRequest;

class CreateCaseTemplateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => ['required', 'unique:case_templates'],
            'description' => 'required',
            'send_on'     => 'required'
        ];
    }
}
