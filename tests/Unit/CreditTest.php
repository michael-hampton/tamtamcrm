<?php

namespace Tests\Unit;

use App\Events\Credit\CreditWasEmailed;
use App\Events\Payment\PaymentWasEmailed;
use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Services\Email\DispatchEmail;
use App\Factory\CreditFactory;
use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\User;
use App\Repositories\CreditRepository;
use App\Requests\SearchRequest;
use App\Search\CreditSearch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Description of InvoiceTest
 *
 * @author michael.hampton
 */
class CreditTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private $customer;

    private $account;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $this->customer->id]);
        $this->customer->contacts()->save($contact);
        $this->account = Account::where('id', 1)->first();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_show_all_the_credits()
    {
        Credit::factory()->create();
        $list = (new CreditSearch(new CreditRepository(new Credit)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_delete_the_credit()
    {
        $credit = Credit::factory()->create();
        $deleted = $credit->deleteEntity();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_archive_the_credit()
    {
        $credit = Credit::factory()->create();
        $deleted = $credit->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_credit()
    {
        $credit = Credit::factory()->create();
        $customer_id = $this->customer->id;
        $data = ['customer_id' => $customer_id];
        $creditRepo = new CreditRepository($credit);
        $updated = $creditRepo->update($data, $credit);
        $found = $creditRepo->findCreditById($credit->id);
        $this->assertInstanceOf(Credit::class, $updated);
        $this->assertEquals($data['customer_id'], $found->customer_id);
    }

    /** @test */
    public function it_can_show_the_credit()
    {
        $credit = Credit::factory()->create();
        $creditRepo = new CreditRepository(new Credit);
        $found = $creditRepo->findCreditById($credit->id);
        $this->assertInstanceOf(Credit::class, $found);
        $this->assertEquals($credit->customer_id, $found->customer_id);
    }

    /** @test */
    public function it_can_create_a_credit()
    {
        //$user = User::factory()->create();
        $user = User::find(5);
        $factory = (new CreditFactory)->create($this->account, $user, $this->customer);

        $data = $this->generateCreditNote();

        $creditRepo = new CreditRepository(new Credit);
        $credit = $creditRepo->create($data, $factory);
        $this->assertEquals($this->customer->id, $credit->customer_id);
        $this->assertInstanceOf(Credit::class, $credit);
        $this->assertEquals($data['number'], $credit->number);
        $this->assertNotEmpty($credit->invitations);
    }

    private function generateCreditNote() {

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
            'status_id'      => Credit::STATUS_DRAFT,
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

        $credit_note_data = $this->generateCreditNote();
        $credit = CreditFactory::create($this->account, $this->user, $this->customer);
        $credit_note = (new CreditRepository(new Credit()))->create($credit_note_data, $credit);

        $template = (new EmailTemplateRepository(new EmailTemplate()))->getTemplateForType('credit');
        (new DispatchEmail($credit_note))->execute(null, $template->subject, $template->message);

        Event::assertDispatched(CreditWasEmailed::class);
    }
}
