<?php

namespace App\Requests\Account;

use App\Repositories\Base\BaseFormRequest;

class StoreAccountRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        $rules['company_logo'] = 'mimes:jpeg,jpg,png,gif|max:10000'; // max 10000kb
        return $rules;
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        $settings = json_decode($input['settings']);

        $input['settings'] = $settings;
        $this->replace($input);
    }
}
