<?php

namespace App\Requests\Payment;

use App\Repositories\Base\BaseFormRequest;
use App\Rules\Payment\CreditPaymentValidation;
use App\Rules\Payment\InvoicePaymentValidation;
use App\Rules\PaymentAppliedValidAmount;
use App\Rules\ValidCreditsPresentRule;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->payment);
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
            'number'   => [
                'nullable',
                Rule::unique('payments')->where(
                    function ($query) {
                        return $query->where('account_id', $this->payment->account_id);
                    }
                )->ignore($this->payment),
            ],
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
