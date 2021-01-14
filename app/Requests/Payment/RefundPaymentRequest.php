<?php

namespace App\Requests\Payment;

use App\Models\Payment;
use App\Repositories\Base\BaseFormRequest;
use App\Rules\Refund\CreditRefundValidation;
use App\Rules\Refund\InvoiceRefundValidation;
use App\Rules\Refund\RefundValidation;


class RefundPaymentRequest extends BaseFormRequest
{
    public function rules()
    {
        $input = $this->all();

        $rules = [
            'id'                    => 'bail|required',
            'id'                    => new RefundValidation($input),
            'amount'                => 'numeric',
            'date'                  => 'required',
            'invoices.*.invoice_id' => 'required',
            'invoices.*.amount'     => 'required',
            'invoices'              => new InvoiceRefundValidation($input),
            'credits'               => new CreditRefundValidation($input),
        ];

        return $rules;
    }

    public function payment(): ?Payment
    {
        $input = $this->all();

        return Payment::whereId($input['id'])->first();
    }

    protected function prepareForValidation()
    {
        $input = $this->all();
        if (!isset($input['gateway_refund'])) {
            $input['gateway_refund'] = false;
        }

        if (!isset($input['send_email'])) {
            $input['send_email'] = false;
        }

        $this->replace($input);
    }
}
