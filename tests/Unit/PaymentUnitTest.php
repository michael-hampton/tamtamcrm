<?php

namespace Tests\Unit;

use App\Components\Currency\CurrencyConverter;
use App\Components\InvoiceCalculator\LineItem;
use App\Components\Payment\DeletePayment;
use App\Components\Payment\Gateways\Stripe;
use App\Components\Payment\Invoice\ReverseInvoicePayment;
use App\Components\Payment\ProcessPayment;
use App\Components\Refund\RefundFactory;
use App\Factory\CreditFactory;
use App\Factory\CustomerFactory;
use App\Factory\InvoiceFactory;
use App\Factory\PaymentFactory;
use App\Models\Account;
use App\Models\CompanyGateway;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerGateway;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\CreditRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Requests\SearchRequest;
use App\Search\PaymentSearch;
use App\Transformations\EventTransformable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentUnitTest extends TestCase
{

    use DatabaseTransactions, EventTransformable, WithFaker;

    /**
     * @var User|Collection|Model|mixed
     */
    private User $user;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var Customer|Collection|Model|mixed
     */
    private Customer $customer;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
    }

    /** @test */
    public function it_can_list_all_the_payments()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'user_id'     => $this->user->id,
            'type_id'     => 1,
            'amount'      => $this->faker->randomFloat()
        ];

        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);

        $paymentRepo = new PaymentRepository(new Payment);
        (new ProcessPayment())->process($data, $paymentRepo, $factory);
        $lists = (new PaymentSearch(new PaymentRepository(new Payment)))->filter(new SearchRequest, $this->account);
        $this->assertNotEmpty($lists);
    }

    /** @test */
    public function it_errors_when_the_payments_is_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $paymentRepo = new PaymentRepository(new Payment);
        $paymentRepo->findPaymentById(999);
    }

    /** @test */
    public function it_can_delete_the_payment()
    {
        $invoice = Invoice::factory()->create();
        $factory = (new PaymentFactory())->create($invoice->customer, $invoice->user, $invoice->account);
        $original_amount = $invoice->total;

        $data = [
            'customer_id' => $this->customer->id,
            'type_id'     => 1,
            'amount'      => $invoice->total
        ];

        $data['invoices'][0]['invoice_id'] = $invoice->id;
        $data['invoices'][0]['amount'] = $invoice->total;

        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);
        $original_paid_to_date = $payment->customer->paid_to_date;
        $this->assertEquals($original_paid_to_date, $invoice->total);

        $payment = $payment->fresh();

        $payment = (new DeletePayment($payment))->execute();

        $invoice = $invoice->fresh();

        $this->assertEquals($payment->customer->paid_to_date, ($original_paid_to_date - $original_amount));
        $this->assertEquals($invoice->balance, $original_amount);
        $this->assertEquals($invoice->total, $original_amount);
        $this->assertEquals($invoice->status_id, Invoice::STATUS_SENT);
        $this->assertEquals($payment->status_id, Payment::STATUS_VOIDED);
        $this->assertNotNull($payment->deleted_at);
    }

    /** @test */
    public function it_can_reverse_the_payment()
    {
        $invoice = Invoice::factory()->create();
        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $original_amount = $invoice->total;

        $data = [
            'customer_id' => $this->customer->id,
            'type_id'     => 1,
            'amount'      => $invoice->total
        ];

        $data['invoices'][0]['invoice_id'] = $invoice->id;
        $data['invoices'][0]['amount'] = $invoice->total;

        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);
        $customer_balance = $payment->customer->balance;
        $customer_paid_to_date = $payment->customer->paid_to_date;
        $payment = (new ReverseInvoicePayment($payment))->execute();
        $this->assertEquals($payment->customer->paid_to_date, ($customer_paid_to_date - $original_amount));
        $this->assertEquals($payment->customer->balance, ($customer_balance + $original_amount));
        $this->assertEquals($invoice->balance, $original_amount);
    }

    public function it_can_archive_the_payment()
    {
        $payment = Payment::factory()->create();
        $deleted = $payment->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_get_the_payments()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'type_id'     => 1,
            'amount'      => $this->faker->randomFloat()
        ];

        $paymentRepo = new PaymentRepository(new Payment);
        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $created = (new ProcessPayment())->process($data, $paymentRepo, $factory);
        $found = $paymentRepo->findPaymentById($created->id);
        $this->assertEquals($data['customer_id'], $found->customer_id);
    }

    /** @test */
//    public function it_errors_updating_the_payments()
//    {
//        $this->expectException(\Illuminate\Database\QueryException::class);
//        $payment = Payment::factory()->create();
//        $paymentRepo = new PaymentRepository($payment);
//        $paymentRepo->updatePayment(['name' => null]);
//    }

    /** @test */
    public function it_can_update_the_payments()
    {
        $payment = Payment::factory()->create();
        $paymentRepo = new PaymentRepository($payment);
        $update = [
            'customer_id' => $this->customer->id,
        ];
        $updated = (new ProcessPayment())->process($update, $paymentRepo, $payment);
        $this->assertInstanceOf(Payment::class, $updated);
        $this->assertEquals($update['customer_id'], $updated->customer_id);
    }

    /** @test */
//    public function it_errors_when_creating_the_payments()
//    {
//        $this->expectException(\Illuminate\Database\QueryException::class);
//        $paymentRepo = new PaymentRepository(new Payment);
//        $paymentRepo->createPayment([]);
//    }
//
    /** @test */
    public function it_can_create_a_payments()
    {
        $invoice = Invoice::factory()->create();
        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $paid_to_date = $this->customer->paid_to_date;
        $balance = $this->customer->balance;

        $data = [
            'customer_id' => $this->customer->id,
            'type_id'     => 1,
            'amount'      => $this->faker->randomFloat()
        ];

        $data['invoices'][0]['invoice_id'] = $invoice->id;
        $data['invoices'][0]['amount'] = $invoice->total;

        $paymentRepo = new PaymentRepository(new Payment);
        $created = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertEquals((float)$created->customer->balance, ($balance - $invoice->balance));
        $this->assertEquals($created->customer->paid_to_date, ($paid_to_date + $invoice->balance));
        $this->assertEquals($data['customer_id'], $created->customer_id);
        $this->assertEquals($data['type_id'], $created->type_id);
    }

    /** @test */
    public function it_can_create_a_payment_with_a_gateway_fee()
    {
        $invoice = Invoice::factory()->create();

        $invoice = (new InvoiceRepository($invoice))->save(
            ['gateway_fee' => 12, 'total' => 800, 'balance' => 800],
            $invoice
        );

        (new InvoiceRepository(new Invoice))->markSent($invoice);

        $payment = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $paid_to_date = $this->customer->paid_to_date;
        $balance = $this->customer->balance;

        $data = [
            'customer_id' => $this->customer->id,
            'type_id'     => 1,
            'amount'      => 800
        ];

        $data['invoices'][0]['invoice_id'] = $invoice->id;
        $data['invoices'][0]['amount'] = 800;

        $paymentRepo = new PaymentRepository(new Payment);
        $created = (new ProcessPayment())->process($data, $paymentRepo, $payment);

        $new_total = 800 + $invoice->gateway_fee;

        $this->assertEquals($created->amount, $new_total);

        $this->assertEquals((float)$created->customer->balance, ($balance - $new_total));
        $this->assertEquals($created->customer->paid_to_date, ($paid_to_date + $new_total));
        $this->assertEquals($data['customer_id'], $created->customer_id);
        $this->assertEquals($data['type_id'], $created->type_id);

        $invoice = $invoice->fresh();

        $this->assertEquals($invoice->balance, 0);
    }

    /** @test */
    public function testPaymentGreaterThanPartial()
    {
        $invoice = Invoice::factory()->create();
        //$invoice = $invoice->service()->calculateInvoiceTotals();
        $invoice->partial = 5.0;
        $invoice->save();

        $paid_to_date = $invoice->customer->paid_to_date;

        (new InvoiceRepository(new Invoice))->markSent($invoice);

        $data = [
            'amount'      => 6.0,
            'customer_id' => $invoice->customer->id,
            'invoices'    => [
                [
                    'invoice_id' => $invoice->id,
                    'amount'     => 6.0
                ],
            ],
            'date'        => '2019/12/12',
        ];

        $original_balance = $invoice->balance;

        $expected_balance = $invoice->customer->balance - 6;

        $factory = (new PaymentFactory())->create($invoice->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertEquals($expected_balance, $payment->customer->balance);
        $this->assertEquals($payment->customer->paid_to_date, (float)($paid_to_date + 6));
        $this->assertEquals($data['customer_id'], $payment->customer_id);
        $this->assertNotNull($payment->invoices());
        $this->assertEquals(1, $payment->invoices()->count());
        $invoice = $payment->invoices()->first();
        $this->assertEquals($invoice->partial, 0);
        $this->assertEquals(($original_balance - 6), $invoice->balance);
    }

    /** @test */
    public function testCreditPayment()
    {
        $client = CustomerFactory::create($this->account, $this->user);
        $client->save();

        $credit = CreditFactory::create($this->account, $this->user, $client);//stub the company and user_id
        $credit->customer_id = $client->id;
        $credit->status_id = Invoice::STATUS_SENT;
        $credit = $credit->service()->calculateInvoiceTotals();
        $credit->total = 50;
        $credit->save();


        $data = [
            'amount'      => 50,
            'customer_id' => $client->id,
            'credits'     => [
                [
                    'credit_id' => $credit->id,
                    'amount'    => $credit->total
                ],
            ],
            'date'        => '2020/12/12',

        ];

        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertNotNull($payment);
        $this->assertEquals(50, $payment->amount);
    }

    /** @test */
    public function testPaymentLessThanPartialAmount()
    {
        $invoice = Invoice::factory()->create();

        $invoice->partial = 5.0;

        $invoice->save();

        (new InvoiceRepository(new Invoice))->markSent($invoice);

        $data = [
            'amount'      => 2.0,
            'customer_id' => $invoice->customer->id,
            'invoices'    => [
                [
                    'invoice_id' => $invoice->id,
                    'amount'     => 2.0
                ],
            ],
            'date'        => '2019/12/12',
        ];

        $original_balance = $invoice->balance;

        $factory = (new PaymentFactory())->create($invoice->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertNotNull($payment);
        $this->assertNotNull($payment->invoices());
        $this->assertEquals(1, $payment->invoices()->count());

        $invoice = $payment->invoices()->first();
        $this->assertEquals($invoice->partial, 3);
        $this->assertEquals(($original_balance - 2), (float)$invoice->balance);
    }

    /** @test */
    public function testBasicRefundValidation()
    {
        $client = CustomerFactory::create($this->account, $this->user);
        $client->save();

        $invoice = InvoiceFactory::create($this->account, $this->user, $client);//stub the company and user_id
        $invoice->customer_id = $client->id;
        $invoice->status_id = Invoice::STATUS_SENT;
        //$invoice->uses_inclusive_Taxes = false;
        $invoice->save();

        $invoice = $invoice->service()->calculateInvoiceTotals();
        $invoice->save();

        $data = [
            'amount'      => 50,
            'customer_id' => $client->id,
            // 'invoices' => [
            //     [
            //     'invoice_id' => $this->invoice->hashed_id,
            //     'amount' => $this->invoice->amount
            //     ],
            // ],
            'date'        => '2020/12/12',

        ];

        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertNotNull($payment);
        $this->assertEquals(50, $payment->amount);


        $data = [
            'id'       => $payment->id,
            'refunded' => 50,
            // 'invoices' => [
            //     [
            //     'invoice_id' => $this->invoice->hashed_id,
            //     'amount' => $this->invoice->amount
            //     ],
            // ],
            'date'     => '2020/12/12',
        ];

        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);
        $this->assertNotNull($payment);
        $this->assertEquals(50, $payment->refunded);
    }

    /** @test */
    public function testRefundClassWithInvoices()
    {
        $invoice = Invoice::factory()->create();

        $line_items[] = (new LineItem)
            ->setQuantity(1)
            ->setUnitPrice(2.0)
            ->calculateSubTotal()
            ->setUnitDiscount(0)
            ->setUnitTax(0)
            ->setProductId($this->faker->word())
            ->setNotes($this->faker->realText(50))
            ->toObject();

        $invoice->line_items = $line_items;
        //$invoice = $invoice->service()->calculateInvoiceTotals();
        $invoice->save();

        (new InvoiceRepository(new Invoice))->markSent($invoice);
        $original_customer_balance = abs($invoice->customer->balance);
        $original_paid_to_date = abs($invoice->customer->paid_to_date);

        $account = $invoice->account;
        $settings = $account->settings;
        $settings->should_archive_invoice = false;
        $account->settings = $settings;
        $account->save();

        $data = [
            'amount'      => $invoice->total,
            'customer_id' => $invoice->customer->id,
            'invoices'    => [
                [
                    'invoice_id' => $invoice->id,
                    'amount'     => $invoice->total
                ],
            ],
            'date'        => '2019/12/12',
        ];

        $factory = (new PaymentFactory())->create($invoice->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $this->assertEquals(($original_customer_balance - $invoice->total), $payment->customer->balance);

        $payment = (new RefundFactory())->createRefund(
            $payment,
            [
                'amount'   => $invoice->total,
                'invoices' => [
                    [
                        'invoice_id' => $invoice->id,
                        'amount'     => $invoice->total
                    ],
                ]
            ],
            new CreditRepository(new Credit)
        );

        $this->assertEquals($invoice->balance, $invoice->total);
        $this->assertEquals($invoice->status_id, 2);
        $this->assertEquals($invoice->total, $payment->refunded);
        $this->assertEquals($original_customer_balance, $payment->customer->balance);
        $this->assertEquals(Payment::STATUS_REFUNDED, $payment->status_id);
        $this->assertEquals($original_paid_to_date, $payment->customer->paid_to_date);
    }

    /** @test */
    public function testRefundClassWithoutInvoices()
    {
        $invoice = Invoice::factory()->create();
        $original_paid_to_date = abs($invoice->customer->paid_to_date);

        (new InvoiceRepository(new Invoice))->markSent($invoice);
        $original_customer_balance = $invoice->customer->balance;

        $account = $invoice->account;
        $settings = $account->settings;
        $settings->should_archive_invoice = false;
        $account->settings = $settings;
        $account->save();


        $data = [
            'amount'      => $invoice->total,
            'customer_id' => $invoice->customer->id,
            'invoices'    => [
                [
                    'invoice_id' => $invoice->id,
                    'amount'     => $invoice->total
                ],
            ],
            'date'        => '2019/12/12',
        ];

        $factory = (new PaymentFactory())->create($invoice->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);
        $this->assertEquals(($invoice->customer->balance - $invoice->total), $payment->customer->balance);

        $payment = (new RefundFactory())->createRefund(
            $payment,
            [
                'amount' => $invoice->total,
            ],
            new CreditRepository(new Credit)
        );

        $this->assertEquals($invoice->total, $payment->refunded);
        $this->assertEquals(Payment::STATUS_REFUNDED, $payment->status_id);
        $this->assertEquals($original_customer_balance, $payment->customer->balance);
        $this->assertEquals($original_paid_to_date, $payment->customer->paid_to_date);
    }

    /** @test */
    public function testConversion()
    {
        $factory = (new PaymentFactory())->create($this->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process(['amount' => 800], $paymentRepo, $factory);

        $converted = (new CurrencyConverter)
            ->setBaseCurrency($payment->account->getCurrency())
            ->setExchangeCurrency($payment->customer->currency)
            ->setAmount(2999.99)
            ->calculate();

        $this->assertNotNull($converted);
    }

    public function testRefundClassWithCredits()
    {
        $credit = Credit::factory()->create();

        $line_items[] = (new LineItem)
            ->setQuantity(1)
            ->setUnitPrice(2.0)
            ->calculateSubTotal()
            ->setUnitDiscount(0)
            ->setUnitTax(0)
            ->setProductId($this->faker->word())
            ->setNotes($this->faker->realText(50))
            ->toObject();

        $credit->line_items = $line_items;
        //$invoice = $invoice->service()->calculateInvoiceTotals();
        $credit->save();

        (new CreditRepository(new Credit()))->markSent($credit);

        $data = [
            'amount'      => $credit->total,
            'customer_id' => $credit->customer->id,
            'credits'     => [
                [
                    'credit_id' => $credit->id,
                    'amount'    => $credit->total
                ],
            ],
            'date'        => '2019/12/12',
        ];

        $factory = (new PaymentFactory())->create($credit->customer, $this->user, $this->account);
        $paymentRepo = new PaymentRepository(new Payment);
        $payment = (new ProcessPayment())->process($data, $paymentRepo, $factory);

        $credit = $payment->credits->first();

        $this->assertEquals(0, $credit->balance);

        $payment = (new RefundFactory())->createRefund(
            $payment,
            [
                'amount'  => $credit->total,
                'credits' => [
                    [
                        'credit_id' => $credit->id,
                        'amount'    => $credit->total
                    ],
                ]
            ],
            new CreditRepository(new Credit)
        );

        $this->assertEquals($credit->balance, $credit->total);
        $this->assertEquals($credit->status_id, 2);
        $this->assertEquals($credit->total, $credit->pivot->refunded);
        $this->assertEquals(-$credit->total, $payment->refunded);

        $this->assertEquals(Payment::STATUS_REFUNDED, $payment->status_id);
    }

    /** @test */
    /*public function test_capture()
    {
        // create invoice
        $payment = Payment::factory()->create();
        $payment->customer_id = 5;
        $payment->account_id = $this->account->id;
        $payment->save();

        $customer_gateway = CustomerGateway::where('is_default', 1)->first();
        $company_gateway = CompanyGateway::where('id', 5)->first();

        $objStripe = new Stripe($payment->customer, $customer_gateway, $company_gateway);

        $reference_number = $objStripe->build($payment->amount, null, false);

        $payment->reference_number = $reference_number;
        $payment->save();

        $objStripe->buildPaymentCapture($payment);


    } */

//    public function testAuthorizeRefund()
//    {
//       $payment = Payment::find(3386);
//       $test = (new RefundFactory())->createRefund($payment, [], new CreditRepository(new Credit()));
//
//       echo '<pre>';
//       print_r($payment);
//       die;
//    }
}

