<?php

namespace App\Requests\Cases;

use App\Models\Cases;
use App\Models\CompanyToken;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

class CreateCaseRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $token_sent = request()->bearerToken();

        if (empty(auth()->user()) && !empty($token_sent)) {
            $token = CompanyToken::whereToken($token_sent)->first();

            $user = $token->user;
            Auth::login($user);
        }

        return auth()->user()->can('create', Cases::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => 'required',
            'message' => 'required',
        ];
    }
}
