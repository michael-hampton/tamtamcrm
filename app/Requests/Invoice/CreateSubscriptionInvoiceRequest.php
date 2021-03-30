<?php

namespace App\Requests\Invoice;

use App\Models\CompanyToken;
use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateSubscriptionInvoiceRequest extends FormRequest
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

        return auth()->user()->can('create', Invoice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id,account_id,' . $this->account_id,
        ];
    }
}
