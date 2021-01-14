<?php

namespace App\Requests\Account;

use App\Repositories\Base\BaseFormRequest;
use App\Rules\ValidSettingsRule;

class UpdateAccountRequest extends BaseFormRequest
{
    public function rules()
    {
        $rules = [];
        //$rules['company_logo'] = 'mimes:jpeg,jpg,png,gif|max:10000'; // max 10000kb
        $rules['industry_id'] = 'integer|nullable';
        $rules['size_id'] = 'integer|nullable';
        $rules['country_id'] = 'integer|nullable';
        $rules['work_email'] = 'email|nullable';
        return $rules;
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        if (isset($input['settings'])) {
            $input['settings'] = json_decode($input['settings']);
        }

        $this->replace($input);
    }

}
