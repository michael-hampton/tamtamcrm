<?php

namespace App\Requests\Payment;

use App\Models\Payment;
use App\Repositories\Base\BaseFormRequest;
use App\Rules\Payment\CreditPaymentValidation;
use App\Rules\Payment\InvoicePaymentValidation;
use App\Rules\PaymentAppliedValidAmount;
use App\Rules\ValidCreditsPresentRule;

class UpdatePaymentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $payment = Payment::find($this->payment_id);
        return auth()->user()->can('update', $payment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invoices' => [
                'array',
                'min:1',
                new InvoicePaymentValidation($this->all()),
                new CreditPaymentValidation($this->all())
            ],
            'number'   => 'nullable|unique:payments,number,' . $this->payment_id . ',id,account_id,' . $this->account_id,
        ];
    }

    protected function prepareForValidation()
    {
        $input = $this->all();

        $invoices = [];
        $credits = [];

        if (!empty($input['invoices'])) {
            foreach ($input['invoices'] as $key => $invoice) {
                if (empty($invoice['invoice_id'])) {
                    continue;
                }

                $invoices[] = $invoice;
            }

            $input['invoices'] = $invoices;
        }

        if (!empty($input['credits'])) {
            foreach ($input['credits'] as $key => $credit) {
                if (empty($credit['credit_id'])) {
                    continue;
                }

                $credits[] = $credit;
            }

            $input['credits'] = $credits;
        }

        $input['is_manual'] = true;

        $this->replace($input);
    }
}
