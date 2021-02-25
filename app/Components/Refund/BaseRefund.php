<?php

namespace App\Components\Refund;

use App\Actions\Transaction\TriggerTransaction;
use App\Components\InvoiceCalculator\LineItem;
use App\Events\Payment\PaymentWasRefunded;
use App\Events\Payment\RefundFailed;
use App\Factory\CreditFactory;
use App\Factory\ErrorLogFactory;
use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\ErrorLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\CreditRepository;

class BaseRefund
{

    /**
     * @var Customer
     */
    protected Customer $customer;

    /**
     * @var array
     */
    protected array $line_items;

    /**
     * @var Payment
     */
    protected Payment $payment;
    /**
     * @var array
     */
    protected array $data;
    /**
     * @var CompanyGateway
     */
    protected CompanyGateway $company_gateway;
    /**
     * @var float
     */
    private float $amount = 0;
    /**
     * @var bool
     */
    private bool $has_invoices = false;
    /**
     * @var CreditRepository
     */
    private CreditRepository $credit_repo;

    /**
     * BaseRefund constructor.
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repo
     */
    public function __construct(Payment $payment, array $data, CreditRepository $credit_repo)
    {
        $this->payment = $payment;
        $this->data = $data;
        $this->credit_repo = $credit_repo;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    protected function setCustomer()
    {
        $this->customer = $this->payment->customer;
    }

    /**
     * @param float $amount
     * @param Invoice|null $invoice
     * @return BaseRefund
     * @return BaseRefund
     */
    protected function createLineItem(float $amount, Invoice $invoice = null)
    {
        if ($invoice !== null) {
            $this->has_invoices = true;
        }

        $this->line_items[] = (new LineItem($invoice))
            ->setQuantity(1)
            ->setUnitPrice($amount)
            ->setProductId('CREDIT')
            ->setNotes(
                !empty($invoice) ? 'REFUND for invoice number ' . $invoice->number : 'REFUND for payment ' . $this->payment->number
            )
            ->setSubTotal($amount)
            ->toObject();

        return $this;
    }

    /**
     * @param float $amount
     * @return BaseRefund
     * @return BaseRefund
     */
    protected function increaseRefundAmount(float $amount)
    {
        if (empty($amount)) {
            return $this;
        }

        $this->amount += $amount;
        return $this;
    }

    /**
     * @param float $amount
     * @return BaseRefund
     * @return BaseRefund
     */
    protected function reduceRefundAmount(float $amount)
    {
        if (empty($amount)) {
            return $this;
        }

        $this->amount -= $amount;
        return $this;
    }

    protected function save()
    {
        $this->increaseRefundTotal();
        $this->setStatus();
        $this->updateCustomer();
        $this->createCreditNote();
        $this->payment->save();

        event(new PaymentWasRefunded($this->payment, $this->data));
    }

    private function increaseRefundTotal()
    {
        $this->payment->refunded += $this->amount;
    }

    private function setStatus()
    {
        $status = (float)abs(
            $this->payment->refunded
        ) === (float)$this->payment->amount ? Payment::STATUS_REFUNDED : Payment::STATUS_PARTIALLY_REFUNDED;

        $this->payment->setStatus($status);
    }

    /**
     * @return mixed
     */
    private function updateCustomer()
    {
        $this->payment->customer->reduceAmountPaid($this->amount);
        $this->payment->customer->increaseBalance($this->amount);
        $this->payment->customer->save();
        return $this;
    }

    /**
     * @return $this
     */
    private function createCreditNote()
    {
        $credit_note = CreditFactory::create($this->payment->account, $this->payment->user, $this->payment->customer);

        $credit_note = $this->credit_repo->createCreditNote(
            [
                'line_items' => $this->line_items,
                'total'      => $this->amount,
                'balance'    => $this->amount
            ],
            $credit_note
        );

        (new TriggerTransaction($credit_note))->execute(
            $this->amount,
            $credit_note->customer->balance,
            "Credit Note refund for payment {$this->payment->number}"
        );

        return $this;
    }

    protected function completeCreditRefund()
    {
        if (!empty($this->data['invoices'])) {
            return false;
        }

        $this->reduceRefundTotal();
        $this->setStatus();
        $this->payment->save();

        event(new PaymentWasRefunded($this->payment, $this->data));

        return true;
    }

    private function reduceRefundTotal()
    {
        $this->payment->refunded -= $this->amount;
    }

    /**
     * @param User $user
     * @param array $errors
     * @return bool
     */
    protected function addErrorToLog(User $user, array $errors): bool
    {
        $error_log = ErrorLogFactory::create($this->customer->account, $user, $this->customer);
        $error_log->data = $errors['data'];
        $error_log->error_type = ErrorLog::REFUND;
        $error_log->error_result = ErrorLog::FAILURE;
        $error_log->entity = $this->company_gateway->gateway_key;

        $error_log->save();

        event(new RefundFailed($this->payment));

        return true;
    }

    /**
     * @param User $user
     * @param array $errors
     * @return bool
     */
    protected function triggerSuccess(User $user, array $data): bool
    {
        $data['payment_id'] = $this->payment->id;

        $error_log = ErrorLogFactory::create($this->customer->account, $user, $this->customer);
        $error_log->data = $data;
        $error_log->error_type = ErrorLog::REFUND;
        $error_log->error_result = ErrorLog::SUCCESS;
        $error_log->entity = $this->company_gateway->gateway_key;

        $error_log->save();

        event(new PaymentWasRefunded($this->payment, $data));

        return true;
    }
}
