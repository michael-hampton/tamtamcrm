<?php

namespace App\Requests;

use App\Repositories\Base\BaseFormRequest;

class UploadRequest extends BaseFormRequest
{

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
