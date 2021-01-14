<?php

namespace App\Requests\Company;

use App\Models\Company;
use App\Repositories\Base\BaseFormRequest;

class CreateCompanyRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Company::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'         => ['required', 'unique:companies'],
            'website'      => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'email'        => ['required', 'string'],
            'address_1'    => ['required', 'string'],
            'city'         => ['required', 'string'],
            'town'         => ['required', 'string'],
            'postcode'     => ['required', 'string'],
            //'company_logo' => 'mimes:jpeg,jpg,png,gif|max:10000', // max 10000kb
            'industry_id'  => 'integer|nullable',
            'country_id'   => 'integer|nullable',
        ];

        $rules['contacts.*.email'] = 'nullable|distinct';

        $contacts = request('contacts');

        if (is_array($contacts)) {
            for ($i = 0; $i < count($contacts); $i++) {
                //$rules['contacts.' . $i . '.email'] = 'nullable|email|distinct';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'unique'                    => trans('validation.unique', ['attribute' => 'name']),
            //'required' => trans('validation.required', ['attribute' => 'email']),
            'contacts.*.email.required' => trans('validation.email', ['attribute' => 'email']),
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        $input['contacts'] = json_decode($input['contacts'], true);

//         if (!isset($input['settings'])) {
//             $input['settings'] = VendorSettings::defaults();
//         }


        $this->replace($input);
    }

}
