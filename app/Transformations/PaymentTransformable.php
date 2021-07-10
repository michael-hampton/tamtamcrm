<?php

namespace App\Transformations;

use App\Models\File;
use App\Models\Payment;
use App\Models\Paymentable;

trait PaymentTransformable
{
    /**
     * @param Payment $payment
     * @return array
     */
    public function transformPayment(Payment $payment, $files = null)
    {
        return [
            'id'                   => (int)$payment->id,
            'number'               => (string)$payment->number ?: '',
            'user_id'              => (int)$payment->user_id,
            'account_id'           => (int)$payment->account_id,
            'company_gateway_id'   => (int)$payment->company_gateway_id,
            'created_at'           => $payment->created_at,
            'assigned_to'          => (int)$payment->assigned_to,
            'customer_id'          => (int)$payment->customer_id,
            'date'                 => $payment->date ?: '',
            'amount'               => (float)$payment->amount,
            'reference_number'     => $payment->reference_number ?: '',
            'invoices'             => $payment->invoices,
            'credits'              => $payment->credits,
            'paymentables'         => !empty($payment->paymentables) ? $this->transformPaymentables(
                $payment->paymentables
            ) : [],
            'deleted_at'           => $payment->deleted_at,
            //$obj->archived_at = $payment->deleted_at;
            'hide'                 => (bool)$payment->hide,
            'payment_method_id'    => (string)$payment->payment_method_id,
            'invitation_id'        => (string)$payment->invitation_id ?: '',
            'invoice_id'           => $payment->invoices->pluck('id')->toArray(),
            'refunded'             => (float)$payment->refunded,
            'is_manual'            => (bool)$payment->is_manual,
            'task_id'              => (int)$payment->task_id,
            'company_id'           => (int)$payment->company_id,
            'applied'              => (float)$payment->applied,
            'internal_note'        => $payment->internal_note ?: '',
            'currency_id'          => (int)$payment->currency_id ?: null,
            'exchange_rate'        => (float)$payment->exchange_rate ?: 1,
            'exchange_currency_id' => (float)$payment->exchange_currency_id ?: '',
            'status_id'            => (int)$payment->status_id,
            'custom_value1'        => $payment->custom_value1 ?: '',
            'custom_value2'        => $payment->custom_value2 ?: '',
            'custom_value3'        => $payment->custom_value3 ?: '',
            'custom_value4'        => $payment->custom_value4 ?: '',
            'files'                => !empty($files) && !empty($files[$payment->id]) ? $this->transformPaymentFiles(
                $files[$payment->id]
            ) : [],
        ];
    }

    public function transformPaymentables($paymentables)
    {
        if (empty($paymentables)) {
            return [];
        }

        return $paymentables->map(
            function (Paymentable $paymentable) {
                return (new PaymentableTransformer())->transform($paymentable);
            }
        )->all();
    }

    private function transformPaymentFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        return $files->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }

}
