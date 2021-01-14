<?php

namespace App\Requests\Company;

use App\Models\Company;
use App\Repositories\Base\BaseFormRequest;

class UpdateCompanyRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $company = Company::find($this->company_id);
        return auth()->user()->can('update', $company);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'         => ['required'],
            'website'      => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'email'        => ['required', 'string'],
            'address_1'    => ['required', 'string'],
            'industry_id'  => 'integer|nullable',
            'country_id'   => 'integer|nullable',
            'city'         => ['required', 'string'],
            'town'         => ['required', 'string'],
            'postcode'     => ['required', 'string'],
            //'company_logo' => 'mimes:jpeg,jpg,png,gif|max:10000|nullable' // max 10000kb
        ];

        $rules['contacts.*.email'] = 'nullable|distinct';


        return $rules;
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

        if (!empty($input['contacts'])) {
            $input['contacts'] = json_decode($input['contacts'], true);

            $cleaned_contacts = [];

            foreach ($input['contacts'] as $key => $contact) {
                if (isset($contact['password']) && strlen($contact['password']) == 0) {
                    unset($input['contacts'][$key]['password']);
                }

                if (trim($contact['first_name']) !== '' && trim($contact['last_name']) !== '') {
                    $cleaned_contacts[] = $contact;
                }
            }

            $input['contacts'] = $cleaned_contacts;
        }

        $this->replace($input);
    }

}
