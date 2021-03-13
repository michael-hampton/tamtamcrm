<?php

namespace App\Requests\User;


use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Hash;

class DeleteUserRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = auth()->user();

        return [
            'password' => [
                'nullable',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        return $fail(__('The password is incorrect.'));
                    }
                }
            ],
        ];
    }
}