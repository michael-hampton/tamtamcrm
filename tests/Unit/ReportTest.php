<?php

namespace Tests\Unit;

use App\Components\Reports\IncomeReport;
use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
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
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportTest extends TestCase
{

    public function test_customer_report()
    {
        (new TaskSearch(new TaskRepository(new Task(), new ProjectRepository(new Project()))))->buildReport(new Request(), Account::where('id', '=', 1)->first());
    }

    public function test_income_report()
    {
        (new IncomeReport())->build(new Request(), Account::find(1));
    }
}