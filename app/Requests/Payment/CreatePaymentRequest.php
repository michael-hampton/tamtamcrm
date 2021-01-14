<?php

namespace App\Requests\Payment;

use App\Models\Payment;
use App\Repositories\Base\BaseFormRequest;
use App\Rules\Payment\CreditPaymentValidation;
use App\Rules\Payment\InvoicePaymentValidation;
use App\Rules\Payment\ValidAmount;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create', Payment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'amount'                => 'numeric|required',
            'amount'                => new ValidAmount($this->all()),
            'date'                  => 'required',
            'customer_id'           => 'bail|required|exists:customers,id',
            'invoices.*.invoice_id' => 'required|distinct|exists:invoices,id',
            'invoices.*.amount'     => 'required',
            'credits.*.credit_id'   => 'required|exists:credits,id',
            'credits.*.amount'      => 'required',
            'invoices'              => new InvoicePaymentValidation($this->all()),
            'credits'               => new CreditPaymentValidation($this->all()),
            'number'                => [
                Rule::unique('recurring_quotes', 'number')->where(
                    function ($query) {
                        return $query->where('customer_id', $this->customer_id)->where('account_id', $this->account_id);
                    }
                )
            ],
        ];

        return $rules;
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
