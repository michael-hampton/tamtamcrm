<?php

namespace App\Rules\User;


use App\Models\Account;
use Illuminate\Contracts\Validation\Rule;

class ValidateUniqueUser implements Rule
{
    private int $account_id;

    private string $username;

    /**
     * ValidateUniqueUser constructor.
     * @param int $account_id
     * @param string $username
     */
    public function __construct(int $account_id, string $username)
    {
        $this->account_id = $account_id;
        $this->username = $username;
    }

    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        $account = Account::where('id', '=', $this->account_id)->first();

        $count = $account->users()->where(
            function ($q) use ($value) {
                $q->where('username', $this->username)->orWhere('email', $value);
            }
        )->count();

        return $count === 0;
    }


    public function message()
    {
        return trans('texts.user_not_unique');
    }
}