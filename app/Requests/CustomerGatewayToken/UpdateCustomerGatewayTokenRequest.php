<?php

namespace App\Requests\CustomerGatewayToken;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerGatewayTokenRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->customer_gateway_token);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id'        => 'required',
            'company_gateway_id' => 'required',
            'gateway_type_id'    => 'required',
            'data'               => 'required'
        ];
    }
}
