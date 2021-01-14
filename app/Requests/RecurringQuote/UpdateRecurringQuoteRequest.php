<?php

namespace App\Requests\RecurringQuote;

use App\Models\RecurringQuote;
use App\Repositories\Base\BaseFormRequest;

class UpdateRecurringQuoteRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $recurring_quote = RecurringQuote::find($this->id);
        return auth()->user()->can('update', $recurring_quote);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invitations.*.contact_id' => 'distinct',
            'frequency'                => 'required',
            'start_date'               => 'required',
            'expiry_date'              => 'required',
            'customer_id'              => 'required|exists:customers,id,account_id,' . auth()->user()->account_user(
                )->account_id,
            'number'                   => 'nullable|unique:recurring_quotes,number,' . $this->id . ',id,account_id,' . $this->account_id,
        ];
    }

}
