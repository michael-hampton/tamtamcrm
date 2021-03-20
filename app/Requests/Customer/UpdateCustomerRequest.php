<?php

namespace App\Requests\Customer;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->customer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                => ['required'],
            'contacts.*.email'    => ['nullable', 'distinct'],
            'number'              => [
                'nullable',
                Rule::unique('customers')->where(
                    function ($query) {
                        return $query->where('account_id', $this->customer->account_id);
                    }
                )->ignore($this->customer),
            ],
            'contacts.*.password' => [
                'sometimes',
                'string',
                'min:10',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                //            ]
            ]
        ];
    }

    public function messages()
    {
        return [
            'unique'        => trans('validation.unique', ['attribute' => 'email']),
            'email'         => trans('validation.email', ['attribute' => 'email']),
            'name.required' => trans('validation.required', ['attribute' => 'name']),
            'required'      => trans('validation.required', ['attribute' => 'email']),
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();
        $cleaned_contacts = [];

        if (!empty($input['contacts'])) {
            foreach ($input['contacts'] as $key => $contact) {
                if (trim($contact['first_name']) !== '' && trim($contact['last_name']) !== '') {
                    $cleaned_contacts[] = $contact;
                }
            }

            $input['contacts'] = $cleaned_contacts;
        }

        $this->replace($input);
    }
}
