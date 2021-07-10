<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\Unit;

use App\Events\Credit\CreditWasEmailed;
use App\Events\PurchaseOrder\PurchaseOrderWasEmailed;
use App\Factory\CreditFactory;
use App\Models\Credit;
use App\Models\EmailTemplate;
use App\Repositories\CreditRepository;
use App\Repositories\EmailTemplateRepository;
use App\Services\Email\DispatchEmail;
use App\Services\PurchaseOrder\Approve;
use App\Factory\OrderFactory;
use App\Factory\PurchaseOrderFactory;
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\NumberGenerator;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\RecurringPurchaseOrder;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Requests\SearchRequest;
use App\Search\PurchaseOrderSearch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Description of PurchaseOrderTest
 *
 * @author michael.hampton
 */
class PurchaseOrderTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private $company;

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
        $this->company = Company::factory()->create();
        $contact = CompanyContact::factory()->create(['company_id' => $this->company->id]);
        $this->company->contacts()->save($contact);
        $this->account = Account::where('id', 1)->first();
        $this->user = User::factory()->create();
        $this->objNumberGenerator = new NumberGenerator;
    }

    /** @test */
    public function it_can_show_all_the_purchase_orders()
    {
        PurchaseOrder::factory()->create();
        $list = (new PurchaseOrderSearch(new PurchaseOrderRepository(new PurchaseOrder)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_update_the_purchase_order()
    {
        $purchase_order = PurchaseOrder::factory()->create();
        $company_id = $this->company->id;
        $data = ['company_id' => 1];
        $purchase_orderRepo = new PurchaseOrderRepository($purchase_order);
        $updated = $purchase_orderRepo->updatePurchaseOrder($data, $purchase_order);
        $found = $purchase_orderRepo->findPurchaseOrderById($purchase_order->id);
        $this->assertInstanceOf(PurchaseOrder::class, $updated);
        $this->assertEquals($data['company_id'], $found->company_id);
    }

    /** @test */
    public function it_can_show_the_purchase_order()
    {
        $purchase_order = PurchaseOrder::factory()->create();
        $purchase_orderRepo = new PurchaseOrderRepository(new PurchaseOrder);
        $found = $purchase_orderRepo->findPurchaseOrderById($purchase_order->id);
        $this->assertInstanceOf(PurchaseOrder::class, $found);
        $this->assertEquals($purchase_order->company_id, $found->company_id);
    }

    /** @test */
    public function it_can_create_a_purchase_order()
    {
        $user = User::find(5);
        $factory = (new PurchaseOrderFactory())->create($this->account, $user, $this->company);

        $data = $this->generatePurchaseOrder();

        $purchase_orderRepo = new PurchaseOrderRepository(new PurchaseOrder);
        $purchase_order = $purchase_orderRepo->createPurchaseOrder($data, $factory);
        $this->assertEquals($this->company->id, $purchase_order->company_id);
        $this->assertInstanceOf(PurchaseOrder::class, $purchase_order);
        $this->assertEquals($data['number'], $purchase_order->number);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_purchase_order_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $purchase_order = new PurchaseOrderRepository(new PurchaseOrder);
        $purchase_order->createPurchaseOrder([]);
    }

    /** @test */
    public function it_errors_finding_a_purchase_order()
    {
        $this->expectException(ModelNotFoundException::class);
        $invoice = new PurchaseOrderRepository(new PurchaseOrder);
        $invoice->findPurchaseOrderById(99999);
    }

    /** @test */
    public function it_can_delete_the_purchase_order()
    {
        $purchase_order = PurchaseOrder::factory()->create();
        $deleted = $purchase_order->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_purchase_order()
    {
        $purchase_order = PurchaseOrder::factory()->create();
        $deleted = $purchase_order->archive();
        $this->assertTrue($deleted);
    }

    public function testPurchaseOrderApproval()
    {
        $purchase_order = PurchaseOrder::factory()->create();
        $purchase_order->setStatus(PurchaseOrder::STATUS_SENT);
        $purchase_order->save();

        $account = $purchase_order->account;
        $settings = $account->settings;
        $settings->should_email_purchase_order = true;
        $settings->should_archive_purchase_order = true;
        $account->settings = $settings;
        $account->save();

        $purchase_order = (new Approve($purchase_order))->execute(new PurchaseOrderRepository(new PurchaseOrder()));

        $this->assertNotNull($purchase_order->deleted_at);
        $this->assertEquals($purchase_order->date_approved->toDateString(), Carbon::now()->toDateString());
        $this->assertInstanceOf(PurchaseOrder::class, $purchase_order);
    }

    private function generatePurchaseOrder() {

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
            'status_id'      => PurchaseOrder::STATUS_DRAFT,
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

        $purchase_order_data = $this->generatePurchaseOrder();
        $purchase_order = PurchaseOrderFactory::create($this->account, $this->user, $this->company);
        $credit_note = (new PurchaseOrderRepository(new PurchaseOrder()))->save($purchase_order_data, $purchase_order);

        $template = (new EmailTemplateRepository(new EmailTemplate()))->getTemplateForType('purchase_order');
        (new DispatchEmail($credit_note))->execute(null, $template->subject, $template->message);

        Event::assertDispatched(PurchaseOrderWasEmailed::class);
    }
}
