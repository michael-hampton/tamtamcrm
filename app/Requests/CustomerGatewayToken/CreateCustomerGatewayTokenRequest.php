<?php

namespace App\Requests\CustomerGatewayToken;

use App\Models\Credit;
use App\Models\CustomerGatewayToken;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreateCustomerGatewayTokenRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', CustomerGatewayToken::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id'        => 'required|exists:customers,id,account_id,' . auth()->user()->account_user()->account_id,
            'company_gateway_id' => 'required',
            'gateway_type_id'    => 'required',
            'data'               => 'required'
        ];
    }
}
