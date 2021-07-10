<?php

namespace App\Requests\CompanyGateway;

use App\Repositories\Base\BaseFormRequest;

class UpdateCompanyGatewayRequest extends BaseFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->company_gateway);
    }

    public function rules()
    {
        $rules = [
        ];

        return $rules;
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        if (isset($input['settings'])) {
            $input['settings'] = json_decode($input['settings']);
        }

        if (isset($input['charges'])) {
            $input['charges'] = json_decode($input['charges']);
        }
        $this->replace($input);
    }

}
