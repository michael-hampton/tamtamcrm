<?php

namespace App\Requests\Email;

use App\Repositories\Base\BaseFormRequest;

class SendEmailRequest extends BaseFormRequest
{
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
