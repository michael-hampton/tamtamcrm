<?php

namespace App\Requests;

use App\Models\Customer;
use App\Models\File;
use App\Models\User;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

class UploadRequest extends BaseFormRequest
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

        if (File::where('account_id', auth()->user()->account_user()->account->id)->count() >= auth()->user()->account_user(
            )->account->getNumberOfAllowedDocuments()) {
            return false;
        }

        return auth()->user()->can('create', File::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filename.*' => 'mimes:doc,pdf,docx,jpg,png,gif',
            'file'       => 'required',
            'entity_id'  => 'required',
            'user_id'    => 'required',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'photos.required'  => 'You must select a file!',
            'task_id.required' => 'There was an unexpected error!',
            'user_id.required' => 'There was an unexpected error!',
        ];
    }

}
