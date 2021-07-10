<?php

namespace App\Requests\Account;

use App\Repositories\Base\BaseFormRequest;

class StoreReminders extends BaseFormRequest
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

        $rules['reminders'] = 'required'; // max 10000kb
        return $rules;
    }
}
