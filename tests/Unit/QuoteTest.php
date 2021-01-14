<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\Unit;

use App\Factory\InvoiceFactory;
use App\Factory\QuoteFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Invoice;
use App\Models\NumberGenerator;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Models\User;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuoteRepository;
use App\Requests\SearchRequest;
use App\Search\QuoteSearch;
use App\Settings\AccountSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Description of QuoteTest
 *
 * @author michael.hampton
 */
class QuoteTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private $customer;

    private $user;

    private $objNumberGenerator;

    /**
     * @var int
     */
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $this->customer->id]);
        $this->customer->contacts()->save($contact);
        $this->account = Account::where('id', 1)->first();
        $this->user = User::factory()->create();
        $this->objNumberGenerator = new NumberGenerator;
    }

    /** @test */
    public function it_can_show_all_the_quotes()
    {
        Quote::factory()->create();
        $list = (new QuoteSearch(new QuoteRepository(new Quote)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_update_the_quote()
    {
        $quote = Quote::factory()->create();
        $customer_id = $this->customer->id;
        $data = ['customer_id' => $customer_id];
        $quoteRepo = new QuoteRepository($quote);
        $updated = $quoteRepo->updateQuote($data, $quote);
        $found = $quoteRepo->findQuoteById($quote->id);
        $this->assertInstanceOf(Quote::class, $updated);
        $this->assertEquals($data['customer_id'], $found->customer_id);
    }

    /** @test */
    public function it_can_show_the_quote()
    {
        $quote = Quote::factory()->create();
        $quoteRepo = new QuoteRepository(new Quote);
        $found = $quoteRepo->findQuoteById($quote->id);
        $this->assertInstanceOf(Quote::class, $found);
        $this->assertEquals($quote->customer_id, $found->customer_id);
    }

    /** @test */
    public function it_can_create_a_quote()
    {
        $factory = (new QuoteFactory())->create($this->account, $this->user, $this->customer);

        $data = [
            'account_id'     => $this->account->id,
            'user_id'        => $this->user->id,
            'customer_id'    => $this->customer->id,
            'total'          => $this->faker->randomFloat(),
            'tax_total'      => $this->faker->randomFloat(),
            'discount_total' => $this->faker->randomFloat(),
            'status_id'      => 1,
        ];

        $quoteRepo = new QuoteRepository(new Quote);
        $quote = $quoteRepo->createQuote($data, $factory);
        $this->assertInstanceOf(Quote::class, $quote);
        $this->assertEquals($data['customer_id'], $quote->customer_id);
        $this->assertNotEmpty($quote->invitations);
    }

    public function test_it_can_create_a_recurring_quote()
    {
        $factory = (new QuoteFactory())->create($this->account, $this->user, $this->customer);

        $data = [
            'account_id'     => $this->account->id,
            'user_id'        => $this->user->id,
            'customer_id'    => $this->customer->id,
            'total'          => $this->faker->randomFloat(),
            'tax_total'      => $this->faker->randomFloat(),
            'discount_total' => $this->faker->randomFloat(),
            'status_id'      => 1,
        ];

        $quoteRepo = new QuoteRepository(new Quote);
        $quote = $quoteRepo->createQuote($data, $factory);

        $arrRecurring = [];

        $arrRecurring['start_date'] = date('Y-m-d');
        $arrRecurring['end_date'] = date('Y-m-d', strtotime('+1 year'));
        $arrRecurring['frequency'] = 30;
        $arrRecurring['recurring_due_date'] = date('Y-m-d', strtotime('+1 month'));
        $recurring_invoice = $quote->service()->createRecurringQuote($arrRecurring);
        $this->assertInstanceOf(RecurringQuote::class, $recurring_invoice);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_quote_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $quote = new QuoteRepository(new Quote);
        $quote->createQuote([]);
    }

    /** @test */
    public function it_errors_finding_a_quote()
    {
        $this->expectException(ModelNotFoundException::class);
        $invoice = new QuoteRepository(new Quote);
        $invoice->findQuoteById(99999);
    }

    /** @test */
    public function it_can_delete_the_quote()
    {
        $quote = Quote::factory()->create();
        $deleted = $quote->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_quote()
    {
        $quote = Quote::factory()->create();
        $deleted = $quote->archive();
        $this->assertTrue($deleted);
    }

    public function testQuoteApproval()
    {
        $quote = Quote::factory()->create();
        $quote->setStatus(Quote::STATUS_SENT);
        $quote->save();

        $account = $quote->account;
        $settings = $account->settings;
        $settings->should_convert_quote = true;
        $settings->should_email_quote = true;
        $settings->should_archive_quote = true;
        $account->settings = $settings;
        $account->save();

        $quote = $quote->service()->approve(new InvoiceRepository(new Invoice), new QuoteRepository(new Quote));

        $this->assertNotNull($quote->deleted_at);
        $this->assertNotNull($quote->invoice_id);
        $this->assertEquals($quote->date_approved->toDateString(), Carbon::now()->toDateString());
        $this->assertInstanceOf(Quote::class, $quote);
    }

    public function testQuoteToOrderConversion()
    {
        $quote = Quote::factory()->create();
        $quote->setStatus(Quote::STATUS_SENT);
        $quote->save();

        $account = $quote->account;
        $settings = $account->settings;
        $settings->should_convert_quote = true;
        $settings->should_email_quote = true;
        $settings->should_archive_quote = true;
        $account->settings = $settings;
        $account->save();

        $order = $quote->service()->convertQuoteToOrder(new OrderRepository(new Order));
        $this->assertNotNull($quote->order_id);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($order->id, $quote->order_id);
    }

    /** @test */
    public function testQuotePadding()
    {
        $customer = Customer::factory()->create();
        $customerSettings = (new AccountSettings())->getAccountDefaults();
        $customerSettings->counter_padding = 5;
        $customerSettings->quote_number_counter = 7;
        $customerSettings->quote_counter_type = 'customer';
        $customer->settings = $customerSettings;
        $customer->save();

        $quote = QuoteFactory::create($this->account, $this->user, $customer);

        $quote_number = $this->objNumberGenerator->getNextNumberForEntity($quote, $customer);
        $this->assertEquals($customer->getSetting('counter_padding'), 5);
        $this->assertEquals($quote_number, '00007');
        $this->assertEquals(strlen($quote_number), 5);
    }

    public function testQuotePrefix()
    {
        $customer = Customer::factory()->create();
        $customerSettings = (new AccountSettings())->getAccountDefaults();
        $customerSettings->quote_number_prefix = 'YEAR';
        $customerSettings->counter_padding = 5;
        $customerSettings->quote_number_counter = 7;
        $customer->settings = $customerSettings;
        $customer->save();

        $quote = QuoteFactory::create($this->account, $this->user, $customer);

        $invoice_number = $this->objNumberGenerator->getNextNumberForEntity($quote, $customer);

        $this->assertEquals($invoice_number, date('Y').'-00007');
    }
}
