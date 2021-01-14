<?php

namespace App\Requests\Customer;

use App\Models\Customer;
use App\Models\Group;
use App\Repositories\Base\BaseFormRequest;

class CreateCustomerRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->account_user()->account->customers->count() >= auth()->user()->account_user(
            )->account->getNumberOfAllowedCustomers()) {
            return false;
        }

        return auth()->user()->can('create', Customer::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //            'address_1' => ['required'],
            'name'                => ['required'],
            'contacts.*.email'    => 'nullable|distinct',
            'contacts.*.password' => [
                'nullable',
                'sometimes',
                'string',
                'min:10',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ]
        ];
    }

    public function messages()
    {
        return [
            'unique'                    => trans('validation.unique', ['attribute' => 'email']),
            //'required' => trans('validation.required', ['attribute' => 'email']),
            'contacts.*.email.required' => trans('validation.email', ['attribute' => 'email']),
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        //is no settings->currency_id is set then lets dive in and find either a group or company currency all the below may be redundant!!
        if (empty($input['currency']) && !empty($input['group_settings_id'])) {
            $group_settings = Group::find($input['group_settings_id']);

            if ($group_settings && property_exists($group_settings->settings, 'currency_id') &&
                isset($group_settings->settings->currency_id)) {
                $input['currency_id'] = (string)$group_settings->settings->currency_id;
            } else {
                $input['currency_id'] = auth()->user()->account_user()->account->settings->currency_id;
            }
        } elseif (empty($input['currency_id'])) {
            $input['currency_id'] = (int)auth()->user()->account_user()->account->settings->currency_id;
        }

        $this->replace($input);
    }
}
