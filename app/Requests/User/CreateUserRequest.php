<?php

namespace App\Requests\User;

use App\Models\User;
use App\Repositories\Base\BaseFormRequest;
use App\Rules\User\ValidateUniqueUser;

class CreateUserRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->account_user()->account->domains->users->count() >= auth()->user()->account_user(
            )->account->getNumberOfAllowedUsers()) {
            return false;
        }

        return auth()->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'department'      => 'nullable|numeric',
            'gender'          => 'nullable|string',
            'job_description' => 'nullable|string',
            'phone_number'    => 'nullable|string',
            'dob'             => 'nullable|string',
            'role'            => 'nullable|array',
            'password'        => [
                'required',
                'string',
                'min:10',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'username'        => ['required', 'string'],
            'profile_photo'   => 'nullable|string',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string:max:100',
            'email'           => ['required', 'string', new ValidateUniqueUser($this->account_id, $this->username)]
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        if (isset($input['company_user'])) {
            if (!isset($input['company_user']['is_admin'])) {
                $input['company_user']['is_admin'] = false;
            }
        }

        $this->replace($input);
    }
}
