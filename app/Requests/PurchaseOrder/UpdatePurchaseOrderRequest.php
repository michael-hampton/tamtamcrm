<?php

namespace App\Requests\Quote;

use App\Models\PurchaseOrder;
use App\Repositories\Base\BaseFormRequest;

class UpdatePurchaseOrderRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $purchase_order = PurchaseOrder::find($this->purchase_order_id);
        return auth()->user()->can('update', $purchase_order);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id'     => 'required',
            'date'           => 'required',
            'due_date'       => 'required',
            'discount_total' => 'required',
            'sub_total'      => 'required',
            'total'          => 'required',
            'tax_total'      => 'required',
            'line_items'     => 'required|array',
            'number'         => 'nullable|unique:purchase_orders,number,' . $this->purchase_order_id . ',id,account_id,' . $this->account_id,
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
