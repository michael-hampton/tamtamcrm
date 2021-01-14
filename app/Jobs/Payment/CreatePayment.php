<?php

namespace App\Jobs\Payment;

use App\Components\InvoiceCalculator\LineItem;
use App\Factory\PaymentFactory;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\CreditRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Traits\CreditPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * Class SaveAttributeValues
 * @package App\Jobs\Attribute
 */
class CreatePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreditPayment;

    private array $data;

    private $ids;

    private Customer $customer;

    /**
     * @var PaymentRepository
     */
    private PaymentRepository $payment_repo;

    /**
     * CreatePayment constructor.
     * @param array $data
     * @param PaymentRepository $payment_repo
     */
    public function __construct(array $data, PaymentRepository $payment_repo)
    {
        $this->data = $data;
        $this->payment_repo = $payment_repo;
    }

    public function handle(): Payment
    {
        if (!empty($this->data['order_id']) && $this->data['order_id'] !== 'null') {
            return $this->createInvoiceFromOrder();
        }

        return $this->createPaymentFromInvoice();
    }

    /**
     * @return Payment
     */
    private function createInvoiceFromOrder(): Payment
    {
        // order to invoice
        $order = Order::where('id', '=', $this->data['order_id'])->first();
        $this->customer = $order->customer;
        $charge_point = $this->customer->getSetting('order_charge_point');
        $order = $order->service()->dispatch(new InvoiceRepository(new Invoice), new OrderRepository(new Order), true);
        $this->ids = $order->invoice_id;
        $this->customer = $order->customer;
        $payment = $this->createPayment($charge_point === 'on_creation');
        $this->attachInvoices($payment, $charge_point === 'on_creation');

        if ($charge_point === 'on_creation') {
            $order->reduceBalance($this->data['amount']);
            $order->payment_taken = true;
        }

        $order->setStatus($charge_point === 'on_creation' ? Order::STATUS_PAID : Order::STATUS_DRAFT);

        $order->payment_id = $payment->id;
        $order->save();

        return $payment;
    }

    private function createPayment(bool $complete_payment = true)
    {
        $payment = PaymentFactory::create($this->customer, $this->customer->user, $this->customer->account);
        $data = [
            'company_gateway_id' => !empty($this->data['company_gateway_id']) ? $this->data['company_gateway_id'] : null,
            'status_id'          => $complete_payment === true ? Payment::STATUS_COMPLETED : Payment::STATUS_PENDING,
            'date'               => Carbon::now(),
            'amount'             => $this->data['amount'],
            'type_id'            => $this->data['payment_type'],
            'reference_number'   => $this->data['payment_method']

        ];

        $payment->fill($data);

        $payment = $this->payment_repo->save($data, $payment, true);

        return $payment;
    }

    /**
     * @param Payment $payment
     * @param bool $complete_payment
     * @return Payment
     */
    private function attachInvoices(Payment $payment, bool $complete_payment = true): Payment
    {
        $invoices = Invoice::whereIn('id', explode(",", $this->ids))
                           ->whereCustomerId($this->customer->id)
                           ->get();

        foreach ($invoices as $invoice) {
            $amount = $invoice->balance;

            if (!empty($this->data['invoices'][$invoice->id]) && !empty($this->data['invoices'][$invoice->id]['gateway_fee'])) {
                $invoice = (new InvoiceRepository($invoice))->save(
                    ['gateway_fee' => $this->data['invoices'][$invoice->id]['gateway_fee']],
                    $invoice
                );

                $amount = $invoice->balance;
            }

            if (!empty($this->data['apply_credits'])) {
                return $this->createCreditsFromInvoice($invoice, $payment);
            }

            if ($complete_payment === true) {
                $this->updateCustomer($payment, $amount);

                $invoice->reduceBalance($amount);
            }

            $payment->attachInvoice($invoice, $amount, true);
            $payment->amount = $amount;
            $payment->applied = $amount;
            $payment->save();
        }

        return $payment;
    }

    /**
     * @param Invoice $invoice
     * @param Payment $payment
     * @return Payment
     */
    private function createCreditsFromInvoice(Invoice $invoice, Payment $payment): Payment
    {
        $credits = $invoice->customer->getActiveCredits();

        $credits = $this->buildCreditsToProcess($credits, $invoice);

        $invoices = $this->getProcessedInvoice();

        $amount = array_sum(array_column($credits, 'amount'));

        $payment->attachInvoice($invoice, $amount, true);

        $payment->applied = $amount;
        $payment->save();

        if (!empty($invoices[$invoice->id])) {
            $invoice->fill($invoices[$invoice->id]);
            $invoice->setStatus(
                (int)$invoices[$invoice->id]['balance'] === 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIAL
            );
            $invoice->save();
        }

        $this->updateCustomer($payment, $amount);

        $this->attachCredits($payment, $invoice, $credits);

        return $payment;
    }

    /**
     * @param Payment $payment
     * @param $amount
     */
    private function updateCustomer(Payment $payment, $amount)
    {
        $payment->customer->reduceBalance($amount);
        $payment->customer->increasePaidToDateAmount($amount);
        $payment->customer->save();
    }

    /**
     * @param Payment $payment
     * @param Invoice $invoice
     * @param $credits_to_process
     * @return Payment
     */
    private function attachCredits(Payment $payment, Invoice $invoice, $credits_to_process): Payment
    {
        $credits_to_process = collect($credits_to_process)->keyBy('credit_id')->toArray();

        $credits = Credit::whereIn('id', array_column($credits_to_process, 'credit_id'))
                         ->whereCustomerId($this->customer->id)
                         ->get();

        foreach ($credits as $credit) {
            if (empty($credits_to_process[$credit->id]['amount'])) {
                continue;
            }

            $payment->attachCredit($credit, $credits_to_process[$credit->id]['amount']);
            $credit = $this->createCreditItem(
                $credits_to_process[$credit->id]['amount'],
                $credit,
                'PAYMENT FOR ' . $invoice->number
            );

            // need to check this
            $credit->transaction_service()->createTransaction(
                $credit->balance * -1,
                $credit->customer->balance,
                'PAYMENT FOR ' . $invoice->number
            );

            $credit->reduceCreditBalance($credits_to_process[$credit->id]['amount']);
            $credit->reduceBalance($credits_to_process[$credit->id]['amount']);

            $credit->setStatus(
                (int)$credit->balance === 0 ? Credit::STATUS_APPLIED : Credit::STATUS_PARTIAL
            );

            $credit->save();
        }

        return $payment->fresh();
    }

    /**
     * @param float $amount
     * @param Credit $credit
     * @param $reference
     * @return Credit|null
     */
    protected function createCreditItem(float $amount, Credit $credit, $reference)
    {
        $line_item = (new LineItem($credit))
            ->setQuantity(1)
            ->setTypeId(Credit::PAYMENT_TYPE)
            ->setUnitPrice($amount)
            ->setProductId('CREDIT')
            ->setNotes($reference)
            ->setSubTotal($amount)
            ->toObject();

        $line_items = $credit->line_items;
        $line_items[] = $line_item;

        $credit = (new CreditRepository($credit))->save(['line_items' => $line_items], $credit);

        return $credit;
    }

    /**
     * @return Payment
     */
    private function createPaymentFromInvoice(): Payment
    {
        $this->ids = $this->data['ids'];
        $this->customer = Customer::find($this->data['customer_id']);

        $payment = $this->createPayment();
        $this->attachInvoices($payment);

        return $payment;
    }
}
