<?php

namespace App\Requests\Lead;

use App\Models\CompanyToken;
use App\Models\Lead;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

class CreateLeadRequest extends BaseFormRequest
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

        return auth()->user()->can('create', Lead::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|unique:leads',
            'name'       => 'required',
            'start_date' => 'nullable',
            //'task_status' => 'required',

        ];
    }

}
