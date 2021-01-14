<?php

namespace App\Jobs\Expense;

use App\Components\Payment\ProcessPayment;
use App\Factory\CloneExpenseToInvoiceFactory;
use App\Factory\PaymentFactory;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $expenses;

    private array $data = [];

    private InvoiceRepository $invoice_repo;

    public function __construct(InvoiceRepository $invoice_repo, $expenses, array $data = [])
    {
        $this->expenses = $expenses;
        $this->invoice_repo = $invoice_repo;
        $this->data = $data;
    }

    public function handle()
    {
        $line_items = [];
        $customer = false;

        $created_expenses = [];

        foreach ($this->expenses as $expense) {
            if ($expense === Expense::STATUS_INVOICED || ($expense->customer->getSetting(
                        'expense_approval_required'
                    ) === true && $expense->status_id !== Expense::STATUS_APPROVED)) {
                continue;
            }

            $notes = $expense->description . '\n';

            if (!empty($customer) && $expense->customer_id !== $customer) {
                continue;
            }

            $line_items[] = [
                'product_id'    => $expense->id,
                'unit_price'    => round($expense->amount * $expense->exchange_rate, 3),
                'quantity'      => 1,
                'type_id'       => Invoice::EXPENSE_TYPE,
                'description'   => !empty($expense->category) ? $expense->category->name : '',
                'unit_discount' => 0
            ];

            $expense->setStatus(Expense::STATUS_INVOICED);
            $expense->save();

            $created_expenses[] = $expense->id;

            $customer = $expense->customer_id;
        }

        if (empty($created_expenses)) {
            return true;
        }

        $first_expense = $this->expenses->first();
        $invoice = CloneExpenseToInvoiceFactory::create($first_expense, $first_expense->user, $first_expense->account);
        $invoice = $this->invoice_repo->createInvoice(['line_items' => $line_items], $invoice);
        Expense::whereIn('id', $created_expenses)->update(['invoice_id' => $invoice->id]);

        if (!empty($this->data) && !empty($this->data['payment_date'])) {
            $this->generatePayment($invoice, $first_expense);
        }
    }

    /**
     * @param Invoice $invoice
     * @param Expense $expense
     * @return Payment|null
     * @return Payment|null
     */
    private function generatePayment(Invoice $invoice, Expense $expense)
    {
        $payment = (new PaymentFactory())->create($invoice->customer, $invoice->user, $invoice->account);

        $data = [
            'reference_number' => $expense->reference_number,
            'type_id'          => $expense->payment_type_id,
            'date'             => $expense->payment_date,
            'amount'           => $invoice->total
        ];

        $data['invoices'][0]['invoice_id'] = $invoice->id;
        $data['invoices'][0]['amount'] = $invoice->total;

        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $payment);

        return $payment;
    }
}
