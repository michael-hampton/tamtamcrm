<?php

namespace App\Requests\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Repositories\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreatePurchaseOrderRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', PurchaseOrder::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id'     => 'required|exists:companies,id,account_id,' . auth()->user()->account_user()->account_id,
            'date'           => 'required',
            'due_date'       => 'required',
            'discount_total' => 'required',
            'sub_total'      => 'required',
            'total'          => 'required',
            'tax_total'      => 'required',
            'line_items'     => 'required|array',
            //'number'         => 'nullable|unique:invoices,number,customer,' . $this->customer_id,
            'number'         => [
                Rule::unique('purchase_orders', 'number')->where(
                    function ($query) {
                        return $query->where('company_id', $this->company_id)->where('account_id', $this->account_id);
                    }
                )
            ],
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
            'comment.required' => 'You must enter a comment!',
            'task_id.required' => 'There was an unexpected error!',
            'user_id.required' => 'There was an unexpected error!',
        ];
    }
}
