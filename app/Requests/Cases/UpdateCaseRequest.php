<?php

namespace App\Requests\Cases;

use App\Models\Cases;
use App\Repositories\Base\BaseFormRequest;

class UpdateCaseRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $case = Cases::find(request()->segment(3));
        return auth()->user()->can('update', $case);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status_id' => 'required',
            'subject'   => 'required',
            'message'   => 'required',
        ];
    }
}
