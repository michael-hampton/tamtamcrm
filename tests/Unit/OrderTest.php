<?php

namespace Tests\Unit;

use App\Components\Payment\Gateways\Stripe;
use App\Factory\CreditFactory;
use App\Factory\OrderFactory;
use App\Jobs\Order\CreateOrder;
use App\Jobs\Payment\CreatePayment;
use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerGateway;
use App\Models\Invoice;
use App\Models\NumberGenerator;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\CreditRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Requests\SearchRequest;
use App\Search\OrderSearch;
use App\Settings\AccountSettings;
use App\Transformations\DepartmentTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{

    use DatabaseTransactions, DepartmentTransformable, WithFaker;

    private Account $account;

    private Customer $customer;

    private User $user;

    private Product $product;

    private NumberGenerator $objNumberGenerator;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $this->customer->id]);
        $this->customer->contacts()->save($contact);
        $this->product = Product::factory()->create();
        $this->objNumberGenerator = new NumberGenerator;
    }

    /** @test */
    public function it_can_show_all_the_orders()
    {
        Order::factory()->create();
        $list = (new OrderSearch(new OrderRepository(new Order)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_update_the_order()
    {
        $order = Order::factory()->create();
        $customer_id = $this->customer->id;
        $data = ['customer_id' => $customer_id];
        $orderRepo = new OrderRepository($order);
        $updated = $orderRepo->updateOrder($data, $order);
        $found = $orderRepo->findOrderById($order->id);
        $this->assertInstanceOf(Order::class, $updated);
        $this->assertEquals($data['customer_id'], $found->customer_id);
    }

    /** @test */
    public function it_can_show_the_order()
    {
        $order = Order::factory()->create();
        $orderRepo = new OrderRepository(new Order);
        $found = $orderRepo->findOrderById($order->id);
        $this->assertInstanceOf(Order::class, $found);
        $this->assertEquals($order->customer_id, $found->customer_id);
    }

    /** @test */
    public function it_can_create_a_order()
    {
        $user = User::factory()->create();
        $factory = (new OrderFactory())->create($this->account, $user, $this->customer);

        $total = $this->faker->randomFloat();

        $data = [
            'account_id'     => $this->account->id,
            'user_id'        => $user->id,
            'customer_id'    => $this->customer->id,
            'total'          => $total,
            'balance'        => $total,
            'tax_total'      => $this->faker->randomFloat(),
            'discount_total' => $this->faker->randomFloat(),
            'status_id'      => 1,
        ];

        $orderRepo = new OrderRepository(new Order);
        $order = $orderRepo->createOrder($data, $factory);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($data['customer_id'], $order->customer_id);
        $this->assertNotEmpty($order->invitations);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_order_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $order = new OrderRepository(new Order);
        $order->createOrder([]);
    }

    /** @test */
    public function it_errors_finding_a_order()
    {
        $this->expectException(ModelNotFoundException::class);
        $order = new OrderRepository(new Order);
        $order->findOrderById(999);
    }

    /** @test */
    public function it_can_delete_the_order()
    {
        $order = Order::factory()->create();
        $deleted = $order->deleteEntity();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_archive_the_order()
    {
        $order = Order::factory()->create();
        $deleted = $order->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function testOrderPadding()
    {
        $customer = Customer::factory()->create();
        $customerSettings = (new AccountSettings)->getAccountDefaults();
        $customerSettings->counter_padding = 5;
        $customerSettings->order_number_counter = 7;
        $customerSettings->order_number_pattern = '{$clientCounter}';
        $customer->settings = $customerSettings;
        $customer->save();

        $order = OrderFactory::create($this->account, $this->user, $customer);

        $order_number = $this->objNumberGenerator->getNextNumberForEntity($order, $customer);
        $this->assertEquals($customer->getSetting('counter_padding'), 5);
        $this->assertEquals($order_number, '00007');
        $this->assertEquals(strlen($order_number), 5);
    }

    /** @test */
    public function it_can_create_a_web_order()
    {
        $data = [
            'source_type'        => 1,
            'title'              => 'New web form request 2020/04/26',
            'task_type'          => 3,
            'task_status_id'     => 9,
            'line_items'         => [
                0 => [
                    'is_amount_discount' => false,
                    'quantity'           => 1,
                    'product_id'         => $this->product->id,
                    'unit_price'         => 12.99,
                    'unit_tax'           => 0,
                    'unit_discount'      => 0,
                ]
            ],
            'sub_total'          => 12.99,
            'is_amount_discount' => false,
            'tax_rate'           => 17.5,
            'total'              => 27.01,
            'valued_at'          => 12.99,
            'shipping_cost'      => 10,
            '_token'             => 'IUQkTOykrK1w98wFNjukdck6A4J0z0uERwOgGIBd',
            'first_name'         => 'Lee',
            'last_name'          => 'Jones',
            'email'              => 'lee.jones@yahoo.com',
            'phone'              => '01425 629322'
        ];

        $order = new Order();
        $order->customer_id = $this->customer->id;

        $order = CreateOrder::dispatchNow(
            $this->account,
            $this->user,
            (object)$data,
            (new CustomerRepository(new Customer)),
            (new OrderRepository(new Order)),
            (new TaskRepository(new Task, new ProjectRepository(new Project))),
            true
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertInstanceOf(Task::class, $order->task);
        $this->assertEquals((float)$order->total, $data['total']);
    }

    /** @test */
    public function testOrderDispatch()
    {
        $order = Order::factory()->create();

        $account = $order->account;
        $settings = $account->settings;
        $settings->should_convert_order = true;
        $settings->should_email_order = true;
        $settings->should_archive_order = true;
        $account->settings = $settings;
        $account->save();

        $order = $order->service()->dispatch(new InvoiceRepository(new Invoice), new OrderRepository(new Order));
        $this->assertInstanceOf(Order::class, $order);
        //$this->assertEquals($order->status_id, Order::STATUS_COMPLETE);

        $invoice = $order->invoice;
        $this->assertNotNull($order->invoice_id);
        $this->assertEquals($invoice->total, $order->total);
        $this->assertEquals($invoice->balance, $order->total);
    }

    /** @test */
    public function testSendOrder()
    {
        $order = Order::factory()->create();
        $orderRepo = new OrderRepository($order);
        $account = $order->account;
        $settings = $account->settings;
        $settings->should_convert_order = true;
        $settings->should_email_order = true;
        $settings->should_archive_order = true;
        $account->settings = $settings;
        $account->save();

        $order->service()->dispatch(new InvoiceRepository(new Invoice), $orderRepo);
        $order = $orderRepo->markSent($order);
        $this->assertInstanceOf(Order::class, $order);

        $this->assertEquals($order->status_id, Order::STATUS_SENT);
    }

    /** @test */
//    public function shipOrder()
//    {
//        $order = factory(Order::class)->create();
//        $order->customer_id = 5;
//
//        $order->save();
//        $objShipping = new ShippoShipment(
//            $order->customer, json_decode(json_encode($order->line_items), true)
//        );
//        $shipping = $objShipping->createShippingProcess();
//
//        $this->assertArrayHasKey('rates', $shipping);
//
//        $rates = $objShipping->getRates();
//        $order->shipping_id = $rates[0]['object_id'];
//        $order->save();
//    }

    /** @test */
    public function cancelOrder()
    {
        $order = Order::factory()->create();
        $order->customer_id = 5;
        $order->save();
        $original_status = $order->status_id;
        $order = $order->service()->cancelOrder();
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(Order::STATUS_CANCELLED, $order->status_id);
        $this->assertEquals($original_status, $order->previous_status);
    }

    /** @test */
    public function test_complete_payment()
    {
        $order = Order::factory()->create();

        $order->customer_id = 5;
        $order->save();
        $original_customer_balance = $order->customer->balance;
        $original_paid_to_date = $order->customer->paid_to_date;

        $account = $order->account;
        $settings = $account->settings;
        $settings->order_charge_point = 'on_creation';
        $account->settings = $settings;
        $account->save();

        $data = [
            'ids'                => $order->id,
            'order_id'           => $order->id,
            'company_gateway_id' => 4,
            'amount'             => $order->total,
            'payment_type'       => 14,
            'payment_method'     => 'abcd'
        ];

        $payment = (new CreatePayment($data, (new PaymentRepository(new Payment))))->handle();
        $invoice = $payment->invoices->first();
        $order = $order->fresh();
        $customer = $order->customer->fresh();

        $this->assertEquals($payment->amount, $order->total);
        $this->assertNotNull($order->invoice_id);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals((float)$payment->amount, $order->total);
        $this->assertEquals(0, $invoice->balance);
        $this->assertEquals($order->status_id, Order::STATUS_PAID);
        $this->assertEquals($invoice->total, $order->total);
        $this->assertTrue($order->payment_taken);

        $new_balance = $original_customer_balance < 0 ? ($original_customer_balance - ($order->total * -1)) : ($original_customer_balance - $order->total);

        $this->assertEquals($customer->balance, $new_balance);
        $this->assertEquals($customer->paid_to_date, ($original_paid_to_date + $order->total));
        $this->assertEquals($payment->status_id, Payment::STATUS_COMPLETED);
        $this->assertEquals($invoice->status_id, Invoice::STATUS_PAID);
    }

    public function test_order_payment_on_send()
    {
        $order = Order::factory()->create();

        $order->customer_id = 5;
        $order->save();
        $original_customer_balance = $order->customer->balance;
        $original_paid_to_date = $order->customer->paid_to_date;

        $account = $order->account;
        $settings = $account->settings;
        $settings->order_charge_point = 'on_send';
        $account->settings = $settings;
        $account->save();

        $data = [
            'ids'                => $order->id,
            'order_id'           => $order->id,
            'company_gateway_id' => 5,
            'amount'             => $order->total,
            'payment_type'       => 14,
            'payment_method'     => 'abcd'
        ];

        $payment = (new CreatePayment($data, (new PaymentRepository(new Payment))))->handle();
        $invoice = $payment->invoices->first();
        $order = $order->fresh();
        $customer = $order->customer->fresh();

        $customer_gateway = CustomerGateway::where('company_gateway_id', $payment->company_gateway_id)->first();

        $ref = (new Stripe($customer, $customer_gateway, $payment->gateway))->build($payment->amount, $invoice, false);
        $payment->reference_number = $ref;
        $payment->save();

        $this->assertNotNull($order->invoice_id);
        $this->assertNotNull($order->payment_id);
        $this->assertFalse($order->payment_taken);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($payment->status_id, Payment::STATUS_PENDING);
        $this->assertEquals((float)$payment->amount, $order->total);
        $this->assertEquals($order->total, $invoice->balance);
        $this->assertEquals($invoice->total, $order->total);
        $this->assertEquals($original_customer_balance, $customer->balance);
        $this->assertEquals($original_paid_to_date, $customer->paid_to_date);
        $this->assertEquals($order->status_id, Order::STATUS_DRAFT);
        $this->assertEquals($invoice->status_id, Invoice::STATUS_SENT);

        $order->service()->send();
        $order = $order->fresh();
        $customer = $customer->fresh();
        $invoice = $invoice->fresh();
        $payment = $payment->fresh();

        $new_balance = $original_customer_balance < 0 ? ($original_customer_balance - ($order->total * -1)) : ($original_customer_balance - $order->total);

        $this->assertEquals($payment->amount, $order->total);
        $this->assertEquals($customer->balance, $new_balance);
        $this->assertEquals($customer->paid_to_date, ($original_paid_to_date + $order->total));
        $this->assertEquals($payment->status_id, Payment::STATUS_COMPLETED);
        $this->assertEquals($invoice->balance, 0);
        $this->assertEquals($invoice->status_id, Invoice::STATUS_PAID);
        $this->assertEquals($order->status_id, Order::STATUS_PAID);
        $this->assertNotNull($payment->reference_number);
    }

    public function testEmail()
    {
        $order = OrderFactory::create($this->account, $this->user, $this->customer);
        $order = (new OrderRepository(new Order()))->save([], $order);

        $template = strtolower('order');
        $subject = $order->customer->getSetting('email_subject_' . $template);
        $body = $order->customer->getSetting('email_template_' . $template);
        $result = $order->service()->sendEmail(null, $subject, $body);
        $this->assertInstanceOf(Order::class, $result);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
