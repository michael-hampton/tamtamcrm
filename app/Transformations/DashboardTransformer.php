<?php

namespace App\Transformations;


use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Expense;
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
        $customers = $customers->map(
            function (Customer $customer) {
                return $this->transformCustomer($customer, ['addresses', 'contacts', 'logs', 'gateways']);
            }
        )->all();

        return $customers;
    }

    private function transformCredits($credits)
    {
        $credits = $credits->map(
            function (Credit $credit) {
                return $this->transformCredit($credit);
            }
        )->all();

        return $credits;
    }

    private function transformExpenses($expenses)
    {
        $expenses = $expenses->map(
            function (Expense $expense) {
                return $this->transformExpense($expense);
            }
        )->all();

        return $expenses;
    }

    private function transformInvoices($invoices)
    {
        $invoices = $invoices->map(
            function (Invoice $invoice) {
                return (new InvoiceTransformable())->transformInvoice($invoice);
            }
        )->all();

        return $invoices;
    }

    private function transformLeads($leads)
    {
        $leads = $leads->map(
            function (Lead $lead) {
                return $this->transformLead($lead);
            }
        )->all();

        return $leads;
    }

    private function transformOrders($orders)
    {
        $orders = $orders->map(
            function (Order $order) {
                return $this->transformOrder($order);
            }
        )->all();
        return $orders;
    }

    private function transformPayments($payments)
    {
        $payments = $payments->map(
            function (Payment $payment) {
                return $this->transformPayment($payment);
            }
        )->all();

        return $payments;
    }

    private function transformQuotes($quotes)
    {
        $quotes = $quotes->map(
            function (Quote $quote) {
                return (new QuoteTransformable())->transformQuote($quote);
            }
        )->all();

        return $quotes;
    }

    private function transformTasks($tasks)
    {
        $tasks = $tasks->map(
            function (Task $task) {
                return $this->transformTask($task);
            }
        )->all();

        return $tasks;
    }
}