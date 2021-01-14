<?php

namespace App\Services\Order;

use App\Components\Payment\Gateways\GatewayFactory;
use App\Models\Customer;
use App\Models\CustomerGateway;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;

class CompleteOrderPayment
{
    /**
     * @var Order
     */
    private Order $order;

    /**
     * CompleteOrderPayment constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function execute(): bool
    {
        $payment = Payment::where('id', $this->order->payment_id)->first();

        $payment = $this->capturePayment($payment);

        if (!$payment) {
            return false;
        }

        $this->updatePayment($payment);

        $this->updateInvoice();
        $this->updateCustomer();
        $this->updateOrder();

        return true;
    }

    private function capturePayment(Payment $payment): ?Payment
    {
        $customer_gateway = CustomerGateway::where('company_gateway_id', $payment->company_gateway_id)->first();

        $company_gateway = $customer_gateway->company_gateway;
        $objGateway = (new GatewayFactory($customer_gateway, $company_gateway))->create($payment->customer);

        $payment = $objGateway->capturePayment($payment);

        return $payment;
    }

    private function updatePayment(Payment $payment): bool
    {
        // update payment
        $payment->setStatus(Payment::STATUS_COMPLETED);
        $payment->save();
        return true;
    }

    private function updateInvoice(): Invoice
    {
        // update invoice
        $invoice = Invoice::where('id', $this->order->invoice_id)->first();
        $invoice->reduceBalance($this->order->total);
        $invoice->setStatus(Invoice::STATUS_PAID);
        $invoice->save();

        return $invoice;
    }

    private function updateCustomer(): Customer
    {
        $this->order->customer->reduceBalance($this->order->total);
        $this->order->customer->increasePaidToDateAmount($this->order->total);
        $this->order->customer->save();

        return $this->order->customer;
    }

    private function updateOrder(): bool
    {
        $this->order->reduceBalance($this->order->total);
        $this->order->setStatus(Order::STATUS_PAID);
        $this->order->payment_taken = true;
        $this->order->save();
        return true;
    }
}
