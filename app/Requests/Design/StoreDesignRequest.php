<?php

namespace App\Requests\Design;

use App\Repositories\Base\BaseFormRequest;

class StoreDesignRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'name' => 'required',
            'name'   => 'required|unique:designs,name,null,null,account_id,' . auth()->user()->account_user(
                )->account_id,
            'design' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        if (!isset($input['design']['product']) || empty($input['design']['product'])) {
            $input['design']['product'] = '';
        }

        if (!isset($input['design']['task']) || empty($input['design']['task'])) {
            $input['design']['task'] = '';
        }

        $this->replace($input);
    }
}
