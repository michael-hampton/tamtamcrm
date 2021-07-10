<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\NumberGenerator;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

/**
 * @test
 * @covers  App\Utils\Traits\GeneratesCounter
 */
class CounterTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create();
        $this->customer = Customer::factory()->create(['account_id' => $this->account->id]);

    }

    /** @test */
    public function test_reset_counter()
    {
        $date_formatted = now()->format('Ymd');

        $settings = $this->account->settings;
        $settings->invoice_number_prefix = 'DATE:Ymd';
        $this->account->settings = $settings;
        $this->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();

        $this->invoice = Invoice::factory()->create(['customer_id' => $this->customer->id, 'account_id' => $this->account->id]);

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0001", $invoice_number);

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0002", $invoice_number);

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0003", $invoice_number);

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0004", $invoice_number);

        $settings->date_counter_next_reset = now()->format('Y-m-d H:i:s');
        $settings->counter_frequency_type = 'DAILY';
        $this->account->settings = $settings;
        $this->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();

        $date_formatted = now()->format('Ymd');

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0001", $invoice_number);

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0002", $invoice_number);

        $settings->date_counter_next_reset = now()->format('Y-m-d');
        $settings->counter_frequency_type = 'DAILY';
        $this->account->settings = $settings;
        $this->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();

        $date_formatted = now()->format('Ymd');

        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($date_formatted . "-0001", $invoice_number);
    }

    public function testQuoteNumberValue()
    {
        $settings = $this->customer->settings;
        $settings->quote_number_counter = 1;
        $this->customer->settings = $settings;
        $this->customer->save();

        $quote = Quote::factory()->create(['customer_id' => $this->customer->id, 'account_id' => $this->account->id]);
        $quote_number = (new NumberGenerator())->getNextNumberForEntity($quote->fresh(), $this->customer->fresh());

        $this->assertEquals($quote_number, 0001);

        $quote_number = (new NumberGenerator())->getNextNumberForEntity($quote->fresh(), $this->customer->fresh());

        $this->assertEquals($quote_number, '0002');
    }

    public function testInvoiceNumberPattern()
    {
        $settings = $this->customer->account->settings;
        $settings->invoice_number_counter = 1;
        $settings->invoice_number_prefix = 'YEAR';

        $this->customer->account->settings = $settings;
        $this->customer->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();
        $this->customer->fresh();

        $this->invoice = Invoice::factory()->create(['customer_id' => $this->customer->id, 'account_id' => $this->account->id]);


        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());
        $invoice_number2 = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($invoice_number, date('Y') . '-0001');

        $this->assertEquals($invoice_number2, date('Y') . '-0002');

        $this->assertEquals($this->customer->fresh()->settings->invoice_number_counter, 3);
    }

    public function testQuoteNumberPattern()
    {
        $settings = $this->customer->account->settings;
        $settings->quote_number_counter = 1;
        $settings->quote_number_prefix = 'YEAR';

        $this->customer->account->settings = $settings;
        $this->customer->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();
        $this->customer->fresh();

        $this->invoice = Quote::factory()->create(['customer_id' => $this->customer->id, 'account_id' => $this->account->id]);


        $invoice_number = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());
        $invoice_number2 = (new NumberGenerator())->getNextNumberForEntity($this->invoice->fresh(), $this->customer->fresh());

        $this->assertEquals($invoice_number, date('Y') . '-0001');

        $this->assertEquals($invoice_number2, date('Y') . '-0002');

        $this->assertEquals($this->customer->fresh()->settings->quote_number_counter, 3);
    }

    public function test_customer_number()
    {
        $settings = $this->customer->account->settings;
        $settings->customer_number_counter = 1;

        $this->customer->account->settings = $settings;
        $this->customer->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();

        $customer_number = (new NumberGenerator())->getNextNumberForEntity($this->customer->fresh(), $this->customer->fresh());

        $this->assertEquals($customer_number, '0001');

        $customer_number = (new NumberGenerator())->getNextNumberForEntity($this->customer->fresh(), $this->customer->fresh());

        $this->assertEquals($customer_number, '0002');
    }

    public function test_customer_number_pattern()
    {
        $settings = $this->account->settings;
        $settings->customer_number_prefix = 'YEAR';
        $settings->customer_number_counter = 1;
        $this->account->settings = $settings;
        $this->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();

        $customer_number = (new NumberGenerator())->getNextNumberForEntity($this->customer->fresh(), $this->customer->fresh());

        $this->assertEquals($customer_number, date('Y') . '-0001');

        $customer_number = (new NumberGenerator())->getNextNumberForEntity($this->customer->fresh(), $this->customer->fresh());

        $this->assertEquals($customer_number, date('Y') . '-0002');

        $settings = $this->account->settings;
        $settings->customer_number_prefix = 'USER';
        $settings->customer_number_counter = 1;
        $this->account->settings = $settings;
        $this->account->save();

        $this->customer->settings = $settings;
        $this->customer->save();

        $customer_number = (new NumberGenerator())->getNextNumberForEntity($this->customer->fresh(), $this->customer->fresh());

        $this->assertEquals($customer_number, $this->customer->user_id . '-0001');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}