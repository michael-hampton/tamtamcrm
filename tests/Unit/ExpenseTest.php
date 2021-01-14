<?php

namespace Tests\Unit;

use App\Factory\ExpenseFactory;
use App\Models\Account;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use App\Repositories\ExpenseRepository;
use App\Requests\SearchRequest;
use App\Search\ExpenseSearch;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    private $customer;

    private $user;

    private $company;

    /**
     * @var int
     */
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->customer = Customer::factory()->create();
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->account = Account::where('id', 1)->first();
    }

    public function it_can_show_all_the_expenses()
    {
        Expense::factory()->create();
        $list = (new ExpenseSearch(new ExpenseRepository(new Expense)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_update_the_expense()
    {
        $expense = Expense::factory()->create();
        $customer_id = $this->customer->id;
        $data = ['customer_id' => $customer_id];
        $expenseRepo = new ExpenseRepository($expense);
        $updated = $expenseRepo->save($data, $expense);
        $found = $expenseRepo->findExpenseById($expense->id);
        $this->assertInstanceOf(Expense::class, $updated);
        $this->assertEquals($data['customer_id'], $found->customer_id);
    }

    /** @test */
    public function it_can_show_the_expense()
    {
        $expense = Expense::factory()->create();
        $quoteRepo = new ExpenseRepository(new Expense);
        $found = $quoteRepo->findExpenseById($expense->id);
        $this->assertInstanceOf(Expense::class, $found);
        $this->assertEquals($expense->customer_id, $found->customer_id);
    }

    /** @test */
    public function it_can_create_a_expense()
    {
        $factory = (new ExpenseFactory)->create($this->user, $this->account);

        $data = [
            'account_id'  => $this->account->id,
            'user_id'     => $this->user->id,
            'customer_id' => $this->customer->id,
            'amount'      => $this->faker->randomFloat(),
            'company_id'  => $this->company->id
        ];

        $expenseRepo = new ExpenseRepository(new Expense);
        $expense = $expenseRepo->createExpense($data, $factory);
        $this->assertInstanceOf(Expense::class, $expense);
        $this->assertEquals($data['customer_id'], $expense->customer_id);
    }

    /** @test */
    public function it_can_create_a_expense_with_invoice()
    {
        $settings = $this->account->settings;
        $settings->expense_auto_create_invoice = true;
        $settings->expense_approval_required = false;
        $this->account->settings = $settings;
        $this->account->save();

        $factory = (new ExpenseFactory)->create($this->user, $this->account);

        $data = [
            'account_id'       => $this->account->id,
            'user_id'          => $this->user->id,
            'customer_id'      => $this->customer->id,
            'amount'           => $this->faker->randomFloat(),
            'company_id'       => $this->company->id,
            'create_invoice'   => true,
            'payment_date'     => Carbon::now()->addDays(10)->format('Y-m-d'),
            'reference_number' => $this->faker->name,
            'payment_type_id'  => 1
        ];

        $expenseRepo = new ExpenseRepository(new Expense);
        $expense = $expenseRepo->createExpense($data, $factory);
        $this->assertInstanceOf(Expense::class, $expense);
        $this->assertEquals($data['customer_id'], $expense->customer_id);

        $expense = $expense->fresh();

        $this->assertNotEmpty($expense->invoice_id);
        $invoice = Invoice::where('id', '=', $expense->invoice_id)->first();
        $this->assertNotEmpty($invoice->payments);
        $payment = $invoice->payments->first();

        $this->assertEquals($payment->reference_number, $expense->reference_number);
        $this->assertEquals($payment->date, $expense->payment_date);
        $this->assertEquals($payment->type_id, $expense->payment_type_id);
    }
}
