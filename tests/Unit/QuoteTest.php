<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\Unit;

use App\Events\Order\OrderWasEmailed;
use App\Events\Quote\QuoteWasEmailed;
use App\Factory\OrderFactory;
use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Services\Email\DispatchEmail;
use App\Services\Quote\Approve;
use App\Services\Quote\ConvertQuoteToOrder;
use App\Services\Quote\GenerateRecurringQuote;
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
use Illuminate\Support\Facades\Event;
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
        $updated = $quoteRepo->update($data, $quote);
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
        $user = User::find(5);
        $factory = (new QuoteFactory())->create($this->account, $user, $this->customer);

        $data = $this->generateQuote();

        $quoteRepo = new QuoteRepository(new Quote);
        $quote = $quoteRepo->create($data, $factory);
        $this->assertInstanceOf(Quote::class, $quote);
        $this->assertEquals($this->customer->id, $quote->customer_id);
        $this->assertEquals($data['number'], $quote->number);
        $this->assertNotEmpty($quote->invitations);
    }

    public function test_it_can_create_a_recurring_quote()
    {
        $factory = (new QuoteFactory())->create($this->account, $this->user, $this->customer);

        $data = $this->generateQuote();

        $quoteRepo = new QuoteRepository(new Quote);
        $quote = $quoteRepo->create($data, $factory);

        $arrRecurring = [];

        $arrRecurring['start_date'] = date('Y-m-d');
        $arrRecurring['end_date'] = date('Y-m-d', strtotime('+1 year'));
        $arrRecurring['frequency'] = 30;
        $arrRecurring['recurring_due_date'] = date('Y-m-d', strtotime('+1 month'));
        $recurring_invoice = (new GenerateRecurringQuote($quote))->execute($arrRecurring);
        $this->assertInstanceOf(RecurringQuote::class, $recurring_invoice);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_quote_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $quote = new QuoteRepository(new Quote);
        $quote->create([]);
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

        $quote = (new Approve($quote))->execute(new InvoiceRepository(new Invoice), new QuoteRepository(new Quote));

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

        $order = (new ConvertQuoteToOrder($quote, new OrderRepository(new Order)))->execute();
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

    private function generateQuote() {

        for ($x = 0; $x < 5; $x++) {
            $line_items[] = (new \App\Components\InvoiceCalculator\LineItem)
                ->setQuantity($this->faker->numberBetween(1, 10))
                ->setUnitPrice($this->faker->randomFloat(2, 1, 1000))
                ->calculateSubTotal()->setUnitDiscount($this->faker->numberBetween(1, 10))
                ->setTaxRateEntity('unit_tax', 10.00)
                ->setProductId($this->faker->word())
                ->setNotes($this->faker->realText(50))
                ->toObject();
        }

        return [
            'account_id'     => 1,
            'status_id'      => Invoice::STATUS_DRAFT,
            'number'         => $this->faker->ean8(),
            'total'          => 800,
            'balance'        => 800,
            'tax_total'      => $this->faker->randomFloat(2),
            'discount_total' => $this->faker->randomFloat(2),
            'hide'           => false,
            'po_number'      => $this->faker->text(10),
            'date'           => $this->faker->date(),
            'due_date'       => $this->faker->date(),
            'line_items'     => $line_items,
            'terms'          => $this->faker->text(500),
            'gateway_fee'    => 12.99
        ];
    }

    public function testEmail()
    {
        Event::fake();

        $quote_data = $this->generateQuote();
        $quote = QuoteFactory::create($this->account, $this->user, $this->customer);
        $quote = (new QuoteRepository(new Quote()))->create($quote_data, $quote);

        $template = (new EmailTemplateRepository(new EmailTemplate()))->getTemplateForType('quote');
        (new DispatchEmail($quote))->execute(null, $template->subject, $template->message);

        Event::assertDispatched(QuoteWasEmailed::class);
    }
}
