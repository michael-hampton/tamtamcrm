<?php

namespace App\Requests\Design;

use App\Repositories\Base\BaseFormRequest;

class UpdateDesignRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required',
            'design' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        if (!isset($input['design']['product']) || empty($input['design']['product']) || is_null(
                $input['design']['product']
            )) {
            $input['design']['product'] = '';
        }

        if (!isset($input['design']['task']) || empty($input['design']['task']) || is_null($input['design']['task'])) {
            $input['design']['task'] = '';
        }
    }
}
