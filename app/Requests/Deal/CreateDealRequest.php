<?php

namespace App\Requests\Deal;

use App\Models\Deal;
use App\Repositories\Base\BaseFormRequest;

class CreateDealRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Deal::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_type' => 'nullable|numeric',
            'rating'      => 'nullable|numeric',
            'customer_id' => 'required|numeric',
            'name'        => 'required|unique:deals',
            'description' => 'required',
            'start_date'  => 'nullable',
            //'task_status' => 'required',

        ];
    }

}
