<?php

namespace App\Requests\Attribute;

use App\Repositories\Base\BaseFormRequest;

class UpdateAttributeRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}