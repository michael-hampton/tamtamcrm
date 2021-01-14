<?php

namespace App\Requests\Lead;

use App\Models\Lead;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $lead = Lead::find($this->lead_id);
        return auth()->user()->can('update', $lead);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => [
                'required',
                Rule::unique('leads')->ignore($this->route('lead_id'))
            ],
            'name'       => 'required',
            'start_date' => 'nullable',
            //'task_status' => 'required',

        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        if (empty($input['industry_id'])) {
            $input['industry_id'] = null;
        }

        $this->replace($input);
    }

}
