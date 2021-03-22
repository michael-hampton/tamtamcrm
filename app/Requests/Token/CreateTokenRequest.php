<?php

namespace App\Requests\Token;

use App\Models\CompanyToken;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Hash;

class CreateTokenRequest extends BaseFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', CompanyToken::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = auth()->user();

        return [
            'name'     => 'required'
        ];
    }
}
