<?php

namespace App\Requests\Email;

use App\Models\Email;
use App\Models\File;
use App\Models\User;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

class SendEmailRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        if(empty(auth()->user())) {
            $user = User::where('id', '=', $this->user_id)->first();
            Auth::login($user);
        }

        if (Email::where('account_id', auth()->user()->account_user()->account->id)->count() >= auth()->user()->account_user(
            )->account->getNumberOfAllowedEmails()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "template"  => "required",
            "entity"    => "required",
            "entity_id" => "required",
            "subject"   => "required",
            "body"      => "required",
        ];
    }

    public function message()
    {
        return [
            'template' => 'Invalid template',
        ];
    }
}
