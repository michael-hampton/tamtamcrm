<?php

namespace Tests\Unit;

use App\Components\Reports\IncomeReport;
use App\Components\Reports\LineItemReport;
use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
use App\Models\TaxRate;
use App\Repositories\CreditRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DealRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\TaskRepository;
use App\Search\CreditSearch;
use App\Search\CustomerSearch;
use App\Search\DealSearch;
use App\Search\ExpenseSearch;
use App\Search\InvoiceSearch;
use App\Search\OrderSearch;
use App\Search\PaymentSearch;
use App\Search\QuoteSearch;
use App\Search\TaskSearch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    public function test_customer_report()
    {
        (new TaskSearch(new TaskRepository(new Task(), new ProjectRepository(new Project()))))->buildReport(new Request(), Account::where('id', '=', 1)->first());
    }

    public function test_income_report()
    {
        (new IncomeReport())->build(new Request(), Account::find(1));
    }

    public function test_line_item_report()
    {
        $account = Account::factory()->create();
        $product = Product::factory()->create(['account_id' => $account->id]);
        $invoice = Invoice::factory()->create(['account_id' => $account->id, 'currency_id' => 1]);
        $invoice2 = Invoice::factory()->create(['account_id' => $account->id, 'currency_id' => 1]);

        $tax = TaxRate::first();

        $data = [
            'unit_tax' => 5,
            'tax_2' => 2.50,
            'tax_3' => 4.25,
            'tax_rate_name' => $tax->name,
            'tax_rate_name_2' => $tax->name,
            'tax_rate_name_3' => $tax->name,
            'tax_rate_id' => $tax->id,
            'tax_rate_id_1' => $tax->id,
            'tax_rate_id_2' => $tax->id,
            'product_id' => $product->id,
            'unit_discount' => 5
        ];

        $line_items = [];

        foreach ($invoice->line_items as $line_item) {
            $line_items[] = array_merge((array)$line_item, $data);
        }

        $invoice->line_items = $line_items;
        $invoice->save();

        $invoice2->line_items = $line_items;
        $invoice2->save();

        (new LineItemReport())->build(new Request(), $account);
    }
}