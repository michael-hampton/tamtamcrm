<?php

namespace App\Requests\CaseTemplate;


use App\Repositories\Base\BaseFormRequest;

class UpdateCaseTemplateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required',
            'description' => 'required',
            'send_on'     => 'required'
        ];
    }
}
