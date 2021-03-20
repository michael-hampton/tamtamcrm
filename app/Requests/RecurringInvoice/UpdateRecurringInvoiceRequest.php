<?php

namespace App\Requests\RecurringInvoice;

use App\Models\RecurringInvoice;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateRecurringInvoiceRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->recurring_invoice);
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
            'number'         => [
                'nullable',
                Rule::unique('recurring_invoices')->where(
                    function ($query) {
                        return $query->where('account_id', $this->recurring_invoice->account_id);
                    }
                )->ignore($this->recurring_invoice),
            ],
        ];
    }

}
