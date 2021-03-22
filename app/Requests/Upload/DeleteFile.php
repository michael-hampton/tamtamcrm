<?php

namespace App\Requests\Upload;

use App\Repositories\Base\BaseFormRequest;
use Illuminate\Support\Facades\Hash;

class DeleteFile extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = auth()->user();
    }
}
