<?php


namespace App\Traits;


use App\Models\Credit;
use App\Models\Invoice;

trait CreditPayment
{
    private $calculated_credit_amount = 0;
    private $processed_invoices = [];

    public function buildCreditsToProcess($credits, Invoice $invoice)
    {
        if (empty($credits)) {
            return true;
        }

        $original_balance = $invoice->balance;

        $this->processed_invoices[$invoice->id] = $invoice->only(['balance', 'partial']);

        foreach ($credits as $credit) {
            $this->processCredits($credit, $invoice);

            $amount = $this->getCalculatedCreditAmount();

            if (empty($amount)) {
                continue;
            }

            $credits_to_process[] = [
                'credit_id' => $credit->id,
                'amount'    => $amount
            ];

            $invoice = $invoice->fill($this->processed_invoices[$invoice->id]);

            if (empty($this->processed_invoices[$invoice->id]['balance'])) {
                break;
            }
        }

        return $credits_to_process;
    }

    private function processCredits(Credit $credit, Invoice $invoice)
    {
        $this->calculated_credit_amount = 0;

        switch (true) {
            case $invoice->partial > 0 && $credit->balance >= $invoice->partial:
                $this->calculated_credit_amount = $invoice->partial;
                $this->processed_invoices[$invoice->id]['balance'] -= $invoice->partial;
                $this->processed_invoices[$invoice->id]['partial'] = 0;
                break;

            case $invoice->partial > 0 && $credit->balance < $invoice->partial:
                $this->calculated_credit_amount = $credit->balance;
                $this->processed_invoices[$invoice->id]['partial'] -= $credit->balance;
                $this->processed_invoices[$invoice->id]['balance'] -= $credit->balance;
                break;

            case $credit->balance >= $invoice->balance:
                $this->calculated_credit_amount = $invoice->balance;
                $this->processed_invoices[$invoice->id]['balance'] = 0;
                break;

            default:
                $this->calculated_credit_amount = $credit->balance;
                $this->processed_invoices[$invoice->id]['balance'] -= $credit->balance;
                break;
        }

        return true;
    }

    public function getCalculatedCreditAmount()
    {
        return $this->calculated_credit_amount;
    }

    public function getProcessedInvoice()
    {
        return $this->processed_invoices;
    }

}