<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\Unit;

use App\Components\InvoiceCalculator\LineItem;
use App\Components\Payment\ProcessPayment;
use App\Factory\CreditFactory;
use App\Factory\InvoiceFactory;
use App\Factory\PaymentFactory;
use App\Jobs\Invoice\AutobillInvoice;
use App\Jobs\Invoice\ProcessReminders;
use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Invoice;
use App\Models\NumberGenerator;
use App\Models\Payment;
use App\Models\Paymentable;
use App\Models\RecurringInvoice;
use App\Models\User;
use App\Repositories\CreditRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use App\Settings\AccountSettings;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Description of InvoiceTest
 *
 * @author michael.hampton
 */
class InvoiceUnitTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    /**
     * @var \App\Models\Customer|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private Customer $customer;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var Account
     */
    private Account $main_account;

    /**
     * @var User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private User $user;

    /**
     * @var NumberGenerator
     */
    private NumberGenerator $objNumberGenerator;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $this->customer->id]);
        $this->customer->contacts()->save($contact);
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        $this->main_account = Account::where('id', 1)->first();
        $this->objNumberGenerator = new NumberGenerator;
    }

    /** @test */
    public function it_can_show_all_the_invoices()
    {
        Invoice::factory()->create();
        $list = (new InvoiceSearch(new InvoiceRepository(new Invoice)))->filter(
            new SearchRequest(),
            $this->main_account
        );
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_update_the_invoice()
    {
        $invoice = Invoice::factory()->create();
        $customer_id = $this->customer->id;
        $data = ['customer_id' => $customer_id];
        $invoiceRepo = new InvoiceRepository($invoice);
        $updated = $invoiceRepo->updateInvoice($data, $invoice);
        $found = $invoiceRepo->findInvoiceById($invoice->id);
        $this->assertInstanceOf(Invoice::class, $updated);
        $this->assertEquals($data['customer_id'], $found->customer_id);
    }

    /** @test */
    public function it_can_show_the_invoice()
    {
        $invoice = Invoice::factory()->create();
        $invoiceRepo = new InvoiceRepository(new Invoice);
        $found = $invoiceRepo->findInvoiceById($invoice->id);
        $this->assertInstanceOf(Invoice::class, $found);
        $this->assertEquals($invoice->customer_id, $found->customer_id);
    }

    public function testMarkInvoicePaidInvoice()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $factory = (new InvoiceFactory())->create($this->main_account, $user, $customer);

        $data = [
            'account_id'     => $this->main_account->id,
            'user_id'        => $user->id,
            'customer_id'    => $this->customer->id,
            'total'          => 200,
            'balance'        => 200,
            'tax_total'      => 0,
            'discount_total' => 0,
            'status_id'      => 1,
        ];

        $invoiceRepo = new InvoiceRepository(new Invoice);
        $invoice = $invoiceRepo->save($data, $factory);
        $invoice_balance = $invoice->balance;
        $client = $invoice->customer;
        $client_balance = $client->balance;
        $invoice = $invoice->service()->createPayment($invoiceRepo, new PaymentRepository(new Payment));

        $this->assertEquals(0, $invoice->balance);

        $this->assertEquals(1, count($invoice->payments));
        $payment = $invoice->payments->first();
        $this->assertEquals(round($invoice->total, 2), $payment->amount);
    }

    /** @test */
    public function it_can_create_a_invoice()
    {
        $user = User::factory()->create();
        $factory = (new InvoiceFactory())->create($this->main_account, $user, $this->customer);

        $total = 1200;

        $data = [
            'date'           => Carbon::now()->format('Y-m-d'),
            'due_date'       => Carbon::now()->addDays(3)->format('Y-m-d'),
            'account_id'     => $this->customer->account->id,
            'user_id'        => $user->id,
            'customer_id'    => $this->customer->id,
            'total'          => $total,
            'balance'        => $total,
            'tax_total'      => $this->faker->randomFloat(),
            'discount_total' => $this->faker->randomFloat(),
            'status_id'      => 1,
        ];

        $invoiceRepo = new InvoiceRepository(new Invoice);
        $invoice = $invoiceRepo->createInvoice($data, $factory);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($data['customer_id'], $invoice->customer_id);
        $this->assertNotEmpty($invoice->invitations);

        $customer_balance = $invoice->customer->balance;

        $invoiceRepo->markSent($invoice);
        $this->assertCount(1, $invoice->transactions);
        $transaction = $invoice->transactions->first();
        $this->assertEquals($invoice->total, $transaction->amount);
        $this->assertEquals($customer_balance + $invoice->total, $transaction->updated_balance);
        $this->assertEquals($invoice->total, $invoice->balance);
    }

    public function test_it_can_generate_recurring()
    {
        $user = User::factory()->create();
        $factory = (new InvoiceFactory())->create($this->main_account, $user, $this->customer);

        $total = $this->faker->randomFloat();

        $data = [
            'account_id'     => $this->main_account->id,
            'user_id'        => $user->id,
            'customer_id'    => $this->customer->id,
            'total'          => $total,
            'balance'        => $total,
            'tax_total'      => $this->faker->randomFloat(),
            'discount_total' => $this->faker->randomFloat(),
            'status_id'      => 1,
        ];

        $invoiceRepo = new InvoiceRepository(new Invoice);
        $invoice = $invoiceRepo->createInvoice($data, $factory);

        $arrRecurring = [];

        $arrRecurring['start_date'] = date('Y-m-d');
        $arrRecurring['expiry_date'] = date('Y-m-d', strtotime('+1 year'));;
        $arrRecurring['frequency'] = 30;
        $arrRecurring['grace_period'] = 0;
        $arrRecurring['due_date'] = date('Y-m-d', strtotime('+1 month'));
        $recurring_invoice = $invoice->service()->createRecurringInvoice($arrRecurring);
        $this->assertInstanceOf(RecurringInvoice::class, $recurring_invoice);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_invoice_when_required_fields_are_not_passed()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $invoice = new InvoiceRepository(new Invoice);
        $invoice->createInvoice([]);
    }

    /** @test */
    public function it_errors_finding_a_invoice()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $invoice = new InvoiceRepository(new Invoice);
        $invoice->findInvoiceById(999);
    }

    /** @test */
    public function it_can_archive_the_invoice()
    {
        $invoice = Invoice::factory()->create();
        $deleted = $invoice->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function testInvoicePadding()
    {
        $customer = Customer::factory()->create();
        $customerSettings = (new AccountSettings())->getAccountDefaults();
        $customerSettings->counter_padding = 5;
        $customerSettings->invoice_number_counter = 7;
        $customerSettings->invoice_counter_type = 'customer';
        $customer->settings = $customerSettings;
        $customer->save();

        $invoice = InvoiceFactory::create($this->main_account, $this->user, $customer);

        $invoice_number = $this->objNumberGenerator->getNextNumberForEntity($invoice, $customer);
        $this->assertEquals($customer->getSetting('counter_padding'), 5);
        $this->assertEquals($invoice_number, '00007');
        $this->assertEquals(strlen($invoice_number), 5);
    }

    public function testInvoicePrefixYear()
    {
        $customer = Customer::factory()->create();
        $customerSettings = (new AccountSettings())->getAccountDefaults();
        $customerSettings->invoice_number_prefix = 'YEAR';
        $customerSettings->counter_padding = 5;
        $customerSettings->invoice_number_counter = 7;
        $customer->settings = $customerSettings;
        $customer->save();

        $invoice = InvoiceFactory::create($this->main_account, $this->user, $customer);

        $invoice_number = $this->objNumberGenerator->getNextNumberForEntity($invoice, $customer);

        $this->assertEquals($invoice_number, date('Y') . '-00007');
    }

    public function testInvoicePrefixCustomer()
    {
        $customer = Customer::factory()->create();
        $customerSettings = (new AccountSettings())->getAccountDefaults();
        $customerSettings->invoice_number_prefix = 'CUSTOMER';
        $customerSettings->counter_padding = 5;
        $customerSettings->invoice_number_counter = 7;
        $customer->settings = $customerSettings;
        $customer->save();

        $invoice = InvoiceFactory::create($this->main_account, $this->user, $customer);

        $invoice_number = $this->objNumberGenerator->getNextNumberForEntity($invoice, $customer);

        $this->assertEquals($invoice_number, $customer->number . '-00007');
    }

    /** @test */
    public function testReverseInvoice()
    {
        $invoice = Invoice::factory()->create();

        $account = $invoice->account;
        $settings = $account->settings;
        $settings->should_archive_invoice = false;
        $account->settings = $settings;
        $account->save();

        $invoice->service()->createPayment(
            new InvoiceRepository(new Invoice),
            new PaymentRepository(new Payment)
        )->save();

        $first_payment = $invoice->payments->first();

        $this->assertEquals((float)$first_payment->amount, (float)$invoice->total);

        $this->assertEquals((float)$first_payment->applied, (float)$invoice->total);

        $this->assertTrue($invoice->isReversable());

        $balance_remaining = $invoice->balance;
        $total_paid = $invoice->total - $invoice->balance;

        /*Adjust payment applied and the paymentables to the correct amount */

        $paymentables = Paymentable::wherePaymentableType(Invoice::class)
                                   ->wherePaymentableId($invoice->id)
                                   ->get();

        $paymentables->each(
            function ($paymentable) use ($total_paid) {
                $paymentable->amount = $paymentable->refunded;
                $paymentable->save();
            }
        );

        /* Generate a credit for the $total_paid amount */
        $credit = CreditFactory::create($invoice->account, $invoice->user, $invoice->customer);
        $credit->customer_id = $invoice->customer_id;

        $item = (new LineItem($credit))
            ->setQuantity(1)
            ->setUnitPrice($total_paid)
            ->setNotes("Credit for reversal of " . $invoice->number);

        $credit->line_items = [$item->toObject()];

        $credit->save();

        $credit = $credit->service()->calculateInvoiceTotals();

        (new CreditRepository(new Credit))->markSent($credit);

        /* Set invoice balance to 0 */
        $invoice->transaction_service()->createTransaction($balance_remaining * -1, $item->getNotes())->save();

        /* Set invoice status to reversed... somehow*/
        $invoice->status_id = Invoice::STATUS_REVERSED;
        $invoice->save();

        /* Reduce client.paid_to_date by $total_paid amount */
        $invoice->customer->paid_to_date -= $total_paid;

        /* Reduce the client balance by $balance_remaining */
        $invoice->customer->balance -= $balance_remaining;

        $invoice->customer->save();
        //create a ledger row for this with the resulting Credit ( also include an explanation in the notes section )
    }

    /** @test */
    public function testReversalViaAPI()
    {
        $invoice = Invoice::factory()->create();
        $invoice->customer->balance = $invoice->balance;
        $invoice->customer->save();

        $this->assertEquals($invoice->customer->balance, $invoice->balance);

        $client_paid_to_date = $invoice->customer->paid_to_date;

        $client_balance = $invoice->customer->balance;
        $invoice_balance = $invoice->balance;
        $invoice_total = $invoice->total;

        $account = $invoice->account;
        $settings = $account->settings;
        $settings->should_archive_invoice = false;
        $account->settings = $settings;
        $account->save();

        (new InvoiceRepository(new Invoice()))->markSent($invoice);

        $this->assertEquals(Invoice::STATUS_SENT, $invoice->status_id);

        $invoice = $invoice->service()->createPayment(
            new InvoiceRepository(new Invoice),
            new PaymentRepository(new Payment)
        );

        $payment = $invoice->payments->first();

        $this->assertEquals($invoice->customer->balance, $client_balance);

        $this->assertEquals($invoice->customer->paid_to_date, ($client_paid_to_date + $invoice_balance));
        $this->assertEquals(0, $invoice->balance);
        $this->assertEquals(Invoice::STATUS_PAID, $invoice->status_id);

        //$client_paid_to_date = $invoice->customer->paid_to_date;

        $invoice = $invoice->service()->reverseInvoicePayment(
            new CreditRepository(new Credit),
            new PaymentRepository(new Payment)
        );

        $this->assertEquals(Invoice::STATUS_REVERSED, $invoice->status_id);
        $this->assertEquals(0, $invoice->balance);

        $new_customer_balance = $client_balance - ($invoice_total - $invoice_balance);

        $this->assertEquals($new_customer_balance, $invoice->customer->balance);
        $this->assertEquals($invoice->customer->paid_to_date, $client_paid_to_date);
    }

    /** @test */
    public function testReversalNoPayment()
    {
        $invoice = Invoice::factory()->create();
        $client_paid_to_date = $invoice->customer->paid_to_date;
        $client_balance = $invoice->customer->balance;
        $invoice_balance = $invoice->balance;

        (new InvoiceRepository(new Invoice()))->markSent($invoice);

        $this->assertEquals(Invoice::STATUS_SENT, $invoice->status_id);

        $this->invoice = $invoice->service()->reverseInvoicePayment(
            new CreditRepository(new Credit),
            new PaymentRepository(new Payment)
        )->save();

        $this->assertEquals(Invoice::STATUS_REVERSED, $invoice->status_id);
        $this->assertEquals(0, $invoice->balance);
        $this->assertEquals($invoice->customer->paid_to_date, ($client_paid_to_date));
        $this->assertEquals($invoice->customer->balance, $client_balance);
    }

    /** @test */
    public function testCancelInvoice()
    {
        $invoice = Invoice::factory()->create();
        $client_balance = $invoice->customer->balance;

        (new InvoiceRepository(new Invoice))->markSent($invoice);

        $this->assertTrue($invoice->isCancellable());

        $this->assertEquals(Invoice::STATUS_SENT, $invoice->status_id);

        $invoice = $invoice->service()->cancelInvoice();

        $this->assertEquals(0, $invoice->balance);
        $this->assertEquals((float)$invoice->customer->fresh()->balance, (float)$client_balance);
        $this->assertEquals(Invoice::STATUS_CANCELLED, $invoice->status_id);
    }

    /** @test */
    public function testDeleteInvoice()
    {
        $invoice = Invoice::factory()->create();
        $invoice->customer->balance = 0;
        $invoice->customer->save();
        $invoice_balance = $invoice->balance;

        $invoice = (new InvoiceRepository(new Invoice))->markSent($invoice);

        (new InvoiceRepository(new Invoice()))->markSent($invoice);
        $this->assertEquals(Invoice::STATUS_SENT, $invoice->status_id);

        $invoice->service()->createPayment(
            new InvoiceRepository(new Invoice),
            new PaymentRepository(new Payment)
        )->save();

        $invoice = $invoice->fresh();

        $client_paid_to_date = $invoice->customer->paid_to_date;
        $client_balance = $invoice->customer->balance;

        $invoice->deleteInvoice();
        $invoice = Invoice::where('id', '=', $invoice->id)->withTrashed()->first();

        $payment = $invoice->payments()->withTrashed()->first()->fresh();

        $this->assertEquals($invoice->customer->fresh()->paid_to_date, ($client_paid_to_date - $invoice_balance));
        $this->assertEquals(0, $invoice->balance);
        $this->assertEquals($invoice->customer->fresh()->balance, 0);
        //$this->assertEquals(Invoice::STATUS_CANCELLED, $invoice->status_id);
        $this->assertTrue($invoice->trashed());
        $this->assertTrue($payment->trashed());
    }

    public function testPartialDelete()
    {
        $first_invoice = Invoice::factory()->create();

        $first_invoice->customer->balance = 0;
        $first_invoice->customer->save();
        $invoice_balance = $first_invoice->balance;

        (new InvoiceRepository(new Invoice))->markSent($first_invoice);

        $second_invoice = Invoice::factory()->create();
        $second_invoice->customer_id = $first_invoice->customer->id;
        $second_invoice->customer->save();

        (new InvoiceRepository(new Invoice))->markSent($second_invoice);

        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $paid_to_date = $this->customer->paid_to_date;
        $balance = $second_invoice->customer->fresh()->balance;

        $data = [
            'customer_id' => $first_invoice->customer->id,
            'type_id'     => 1,
            'amount'      => $first_invoice->balance + $second_invoice->balance
        ];

        $data['invoices'][0]['invoice_id'] = $first_invoice->id;
        $data['invoices'][0]['amount'] = $first_invoice->total;
        $data['invoices'][1]['invoice_id'] = $second_invoice->id;
        $data['invoices'][1]['amount'] = $second_invoice->total;

        $paymentRepo = new PaymentRepository(new Payment);
        $created = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertEquals(
            (float)$created->customer->balance,
            ($balance - ($first_invoice->balance + $second_invoice->balance))
        );
        $this->assertEquals(
            $created->customer->paid_to_date,
            ($paid_to_date + ($first_invoice->balance + $second_invoice->balance))
        );

        $first_invoice = $first_invoice->fresh();

        $this->assertEquals($data['customer_id'], $created->customer_id);
        $this->assertEquals($data['type_id'], $created->type_id);
        $this->assertEquals($first_invoice->status_id, Invoice::STATUS_PAID);

        $first_invoice->deleteInvoice();
        $invoice = Invoice::where('id', '=', $first_invoice->id)->withTrashed()->first();

        $this->assertEquals($first_invoice->customer->fresh()->paid_to_date, ($data['amount'] - $invoice_balance));

        $payment = $invoice->payments()->withTrashed()->first()->fresh();

        $customer = $payment->customer->fresh();

        $this->assertEquals($customer->balance, 0);

        $this->assertEquals($payment->amount, ($created->amount - $first_invoice->total));
        $this->assertFalse($payment->trashed());
        $this->assertTrue($invoice->trashed());
    }

    /** @test */
    public function testCancellationReversal()
    {
        $invoice = Invoice::factory()->create();
        $invoice->customer->balance = 0;
        $invoice->customer->save();

        $previous_balance = $invoice->balance;
        $customer_balance = $invoice->customer->balance; //0

        //800

        (new InvoiceRepository(new Invoice()))->markSent($invoice);
        $balance_with_invoice = $invoice->customer->fresh()->balance; //800

        $invoice->service()->cancelInvoice();
        $this->assertEquals(0, $invoice->balance);
        $this->assertEquals(Invoice::STATUS_CANCELLED, $invoice->status_id);
        $this->assertEquals($customer_balance, $invoice->customer->fresh()->balance);
        $invoice->service()->reverseStatus();

        $this->assertEquals(Invoice::STATUS_SENT, $invoice->status_id);
        $this->assertEquals($previous_balance, $invoice->balance);
        $this->assertNull($invoice->previous_status);
        $this->assertNull($invoice->previous_balance);
        $this->assertEquals($invoice->customer->fresh()->balance, $balance_with_invoice);
    }

    /** @test */
    public function autoBill()
    {
        // create invoice
        $invoice = Invoice::factory()->create();
        $invoice->customer_id = 5;
        $invoice->gateway_fee = 0;
        $invoice->save();

        $total = $invoice->total;
        $line_item_count = count($invoice->line_items);

        $invoiceRepo = new InvoiceRepository(new Invoice);
        $original_invoice = $invoiceRepo->createInvoice([], $invoice);

        // auto bill
        AutobillInvoice::dispatchNow($original_invoice, $invoiceRepo);

        $payment = $original_invoice->payments->first();

        $invoice = $original_invoice->fresh();

        $expected_amount = $total + $invoice->gateway_fee;

        $this->assertEquals($line_item_count + 1, count($invoice->line_items));
        $this->assertNotNull($payment);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals((float)$payment->amount, $expected_amount);
        $this->assertEquals(0, $invoice->balance);
    }

    /** @test */
    public function autoBill_with_gateway()
    {
        // create invoice
        $invoice = Invoice::factory()->create();
        $invoice->customer_id = 5;
        $invoice->gateway_fee = 0;

        $total = $invoice->total;
        $line_item_count = count($invoice->line_items);

        $invoiceRepo = new InvoiceRepository(new Invoice);
        $original_invoice = $invoiceRepo->createInvoice([], $invoice);
        $this->assertEquals($total, $original_invoice->total);
        $this->assertEquals($line_item_count, count($original_invoice->line_items));

        $original_customer_balance = $invoice->customer->balance;

        // auto bill
        AutobillInvoice::dispatchNow($original_invoice, $invoiceRepo);
        $invoice = $original_invoice->fresh();

        $customer = $invoice->customer->fresh();

        // invoice total + gateway fee
        $original_customer_balance = $original_customer_balance < 0 ? $original_customer_balance + (1.50 * -1) : $original_customer_balance + 1.50;
        $expected_balance = $original_customer_balance < 0 ? ($original_customer_balance - ($invoice->total * -1)) : ($original_customer_balance - $invoice->total);

        $this->assertEquals($customer->balance, $expected_balance);
        $this->assertEquals($line_item_count + 1, count($invoice->line_items));
        $this->assertEquals($total + $invoice->gateway_fee, $invoice->total);

        $payment = $original_invoice->payments->first();

        $invoice = $original_invoice->fresh();
        $this->assertNotNull($payment);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals((float)$payment->amount, $invoice->total);
        $this->assertEquals(0, $invoice->balance);
    }

    /** @test */
    public function test_reminders()
    {
        // create invoice
        $invoice = Invoice::factory()->create();
        $invoice->customer_id = 5;
        $invoice->status_id = Invoice::STATUS_SENT;
        $invoice->account_id = $this->account->id;
        $invoice->date_to_send = Carbon::now();
        $invoice->save();

        $settings = $this->account->settings;
        $settings->amount_to_charge_1 = 10;
        $settings->reminder1_enabled = true;
        $settings->number_of_days_after_1 = 1;
        $settings->scheduled_to_send_1 = 'after_invoice_date';
        $settings->inclusive_taxes = false;
        $this->account->settings = $settings;
        $this->account->save();

        $invoiceRepo = new InvoiceRepository(new Invoice);

        $original_customer_balance = $invoice->customer->balance;

        $new_balance = $original_customer_balance < 0 ? $original_customer_balance + 10 * -1 : $original_customer_balance + 10;

        ProcessReminders::dispatchNow($invoiceRepo);

        $updated_invoice = $invoice->fresh();

        $this->assertEquals(($invoice->total + 10), $updated_invoice->total);
        $this->assertEquals(($invoice->balance + 10), $updated_invoice->balance);
        $this->assertEquals($new_balance, $updated_invoice->customer->balance);

        $date_to_send = Carbon::parse($invoice->date)->addDays($settings->number_of_days_after_1)->format('Y-m-d');

        $this->assertEquals(count($invoice->line_items) + 1, count($updated_invoice->line_items));
        $this->assertEquals(10, $updated_invoice->late_fee_charge);
        $this->assertEquals($updated_invoice->date_to_send->format('Y-m-d'), $date_to_send);
    }

    /** @test */
    public function test_reminders_percentage()
    {
        // create invoice
        $invoice = Invoice::factory()->create();
        $invoice->customer_id = 5;
        $invoice->account_id = $this->account->id;
        $invoice->date_to_send = Carbon::now();
        $invoice->save();

        $settings = $invoice->customer->account->settings;
        $settings->amount_to_charge_1 = 0;
        $settings->percent_to_charge_1 = 5;
        $settings->reminder1_enabled = true;
        $settings->number_of_days_after_1 = 1;
        $settings->scheduled_to_send_1 = 'after_invoice_date';
        $settings->inclusive_taxes = false;
        $invoice->customer->account->settings = $settings;
        $invoice->customer->account->save();

        $original_customer_balance = $invoice->customer->balance;

        $invoiceRepo = new InvoiceRepository(new Invoice);

        ProcessReminders::dispatchNow($invoiceRepo);

        $updated_invoice = $invoice->fresh();

        $new_balance = $original_customer_balance < 0 ? $original_customer_balance + 40 * -1 : $original_customer_balance + 40;


        $this->assertEquals(($invoice->total + 40), $updated_invoice->total);
        $this->assertEquals(($invoice->balance + 40), $updated_invoice->balance);
        $this->assertEquals($new_balance, $updated_invoice->customer->balance);

        $date_to_send = Carbon::parse($invoice->date)->addDays($settings->number_of_days_after_1)->format('Y-m-d');

        $this->assertEquals(count($invoice->line_items) + 1, count($updated_invoice->line_items));
        $this->assertEquals(40, $updated_invoice->late_fee_charge);
        $this->assertEquals($updated_invoice->date_to_send->format('Y-m-d'), $date_to_send);
    }

    /* public function test_buy_now_link()
    {
        $invoice = Invoice::factory()->create();
        $invoice->customer_id = 5;

        $products = Product::get();

        $line_items = [];

        foreach ($products as $key => $product) {
            $line_items[] = (new \App\Components\InvoiceCalculator\LineItem)
                ->setQuantity(1)
                ->setUnitPrice($product->price)
                ->calculateSubTotal()
                ->setUnitDiscount(0)
                ->setUnitTax(0)
                ->setProductId($product->id)
                ->setNotes($product->description)
                ->toObject();

            if ($key > 5) {
                break;
            }
        }

        $invoice->line_items = $line_items;

        $invoice->save();

        $company_gateway = CompanyGateway::where('gateway_key', '=', '64bcbdce')->first();

        $objPaypal = new PaypalExpress($invoice->customer, $company_gateway);

        $options = array(
            'amount'               => $invoice->balance,
            'returnUrl'            => $objPaypal->getReturnUrl($invoice, $invoice->customer, $invoice->balance),
            'cancelUrl'            => 'https://www.example.com/cancel',
            'transactionReference' => $invoice->id,
            'currency'             => $invoice->customer->currency->iso_code
        );

        $response = $objPaypal->purchase($options, $invoice);
        $data = $response->getData();

        $options['TOKEN'] = $data['TOKEN'];

        $response = $objPaypal->complete($options);
    } */
}
