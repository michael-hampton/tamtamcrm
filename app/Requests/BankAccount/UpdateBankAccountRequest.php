<?php

namespace App\Requests\BankAccount;

use App\Repositories\Base\BaseFormRequest;

class UpdateBankAccountRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
