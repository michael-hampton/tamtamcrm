<?php

namespace App\Transformations;


use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Expense;
use App\Models\File;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\Task;

class DashboardTransformer
{
    use CustomerTransformable;
    use CreditTransformable;
    use ExpenseTransformable;
    use LeadTransformable;
    use OrderTransformable;
    use PaymentTransformable;
    use TaskTransformable;

    public function transformDashboardData(Account $account)
    {
        return [
            'customers' => $this->transformCustomers($account->customers),
            'invoices'  => $this->transformInvoices($account->invoices),
            'credits'   => $this->transformCredits($account->credits),
            'payments'  => $this->transformPayments($account->payments),
            'quotes'    => $this->transformQuotes($account->quotes),
            'orders'    => $this->transformOrders($account->orders),
            'expenses'  => $this->transformExpenses($account->expenses),
            'tasks'     => $this->transformTasks($account->tasks),
            'deals'     => $this->transformLeads($account->leads)
        ];
    }

    private function transformCustomers($customers)
    {
        $contacts = CustomerContact::all()->groupBy('customer_id');
        $files = File::where('fileable_type', '=', 'App\Models\Customer')->get()->groupBy('fileable_id');

        $customers = $customers->map(
            function (Customer $customer) use ($contacts, $files) {
                return $this->transformCustomer($customer, $contacts, $files);
            }
        )->all();

        return $customers;
    }

    private function transformCredits($credits)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Credit')->get()->groupBy('fileable_id');

        $credits = $credits->map(
            function (Credit $credit) use ($files) {
                return $this->transformCredit($credit, $files);
            }
        )->all();

        return $credits;
    }

    private function transformExpenses($expenses)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Expense')->get()->groupBy('fileable_id');

        $expenses = $expenses->map(
            function (Expense $expense) use ($files) {
                return $this->transformExpense($expense, $files);
            }
        )->all();

        return $expenses;
    }

    private function transformInvoices($invoices)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Invoice')->get()->groupBy('fileable_id');

        $invoices = $invoices->map(
            function (Invoice $invoice) use ($files) {
                return (new InvoiceTransformable())->transformInvoice($invoice, $files);
            }
        )->all();

        return $invoices;
    }

    private function transformLeads($leads)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Lead')->get()->groupBy('fileable_id');

        $leads = $leads->map(
            function (Lead $lead) use ($files) {
                return $this->transformLead($lead, $files);
            }
        )->all();

        return $leads;
    }

    private function transformOrders($orders)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Order')->get()->groupBy('fileable_id');

        $orders = $orders->map(
            function (Order $order) use ($files) {
                return $this->transformOrder($order, $files);
            }
        )->all();
        return $orders;
    }

    private function transformPayments($payments)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Payment')->get()->groupBy('fileable_id');

        $payments = $payments->map(
            function (Payment $payment) use ($files) {
                return $this->transformPayment($payment, $files);
            }
        )->all();

        return $payments;
    }

    private function transformQuotes($quotes)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Quote')->get()->groupBy('fileable_id');

        $quotes = $quotes->map(
            function (Quote $quote) use ($files) {
                return (new QuoteTransformable())->transformQuote($quote, $files);
            }
        )->all();

        return $quotes;
    }

    private function transformTasks($tasks)
    {
        $files = File::where('fileable_type', '=', 'App\Models\Task')->get()->groupBy('fileable_id');

        $tasks = $tasks->map(
            function (Task $task) use ($files) {
                return $this->transformTask($task, $files);
            }
        )->all();

        return $tasks;
    }
}