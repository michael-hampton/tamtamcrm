<?php

namespace App\Console\Commands;

use App\Components\InvoiceCalculator\LineItem;
use App\Factory\InvoiceFactory;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Product;
use App\Repositories\InvoiceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Class SendRecurringInvoices.
 */
class CalculateCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate-commission';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates outstanding commission on invoices.';
    private $calculate_total = true;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $invoices = Invoice::where('commission_paid', '=', 0)->where('status_id', '=', Invoice::STATUS_PAID)->get();

        $items = [];
        $invoice_ids = [];

        foreach ($invoices as $invoice) {
            $line_items = $invoice->line_items;
            $subtotal = 0;
            $calculated_fee = 0;

            foreach ($line_items as $line_item) {
                if (!empty($line_item->transaction_fee) && $line_item->transaction_fee > 0) {
                    $commission = $line_item->transaction_fee;

                    $product = Product::whereId($line_item->product_id)->first();
                    $account = $product->account;

                    $subtotal += $line_item->unit_price * $line_item->quantity;
                    $calculated_commission = $this->calculate($commission, $line_item->unit_price);
                    $calculated_fee = $line_item->quantity > 0 ? $calculated_commission *= $line_item->quantity : $calculated_commission;

                    $items[$account->id][$invoice->id] =
                        [
                            'invoice'          => $invoice,
                            'account'          => $account,
                            'line_total'       => $subtotal,
                            'calculated_total' => $calculated_fee,
                            'product_id'       => $product->id
                        ];

                    $invoice_ids[] = $invoice->id;
                }
            }
        }

        if (count($invoice_ids) === 0) {
            return true;
        }

        $success = true;

        foreach ($items as $account_id => $payable_invoices) {
            // $calculated_total = array_sum(array_column($payable_invoice, 'calculated_total'));
            //$line_total = array_sum(array_column($payable_invoice, 'line_total'));

            foreach ($payable_invoices as $invoice_id => $payable_invoice) {
                $total = $payable_invoice['calculated_total'];

                echo $total . ' - ';

                if ($this->calculate_total) {
                    $account_commission = $payable_invoice['account']->transaction_fee;
                    $total = $this->calculate($account_commission, $payable_invoice['line_total']);
                }

                if (!$this->createInvoice($payable_invoice['account'], $payable_invoice['invoice'], $total)) {
                    $success = false;
                }
            }
        }

        if ($success) {
            Invoice::whereIn('id', $invoice_ids)->update(
                ['commission_paid' => true, 'commission_paid_date' => Carbon::now()]
            );
        }

        echo count($invoice_ids) . ' have been updated';

        return true;
    }

    /**
     * @param $commission_amount
     * @param $total
     * @return float
     */
    public function calculate($commission_amount, $total): float
    {
        return $total * $commission_amount / 100;
    }

    /**
     * @param Account $account
     * @param Invoice $invoice
     * @param float $total_paid
     * @return Invoice
     */
    private function createInvoice(Account $account, Invoice $invoice, float $total_paid): Invoice
    {
        if (empty($account->domains) || empty($account->domains->user_id)) {
            $account = $account->service()->convertAccount();
        }

        $customer = $account->domains->customer;
        $user = $account->domains->user;

        $invoice = InvoiceFactory::create($account, $user, $customer);

        $line_items[] = (new LineItem)
            ->setQuantity(1)
            ->setUnitPrice($total_paid)
            ->setTypeId(Invoice::COMMISSION_TYPE)
            ->setNotes("Commission for {$invoice->getNumber()}")
            ->toObject();

        $invoice = (new InvoiceRepository(new Invoice))->save(['line_items' => $line_items], $invoice);

        echo 'created invoice ' . $invoice->getNumber();

        return $invoice;
    }
}
