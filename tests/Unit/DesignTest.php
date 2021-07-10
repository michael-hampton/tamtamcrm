<?php

namespace Tests\Unit;

use App\Services\Pdf\GeneratePdf;
use App\Components\Pdf\GenerateHtml;
use App\Components\Pdf\InvoicePdf;
use App\Factory\DesignFactory;
use App\Jobs\Invoice\CreateInvoicePdf;
use App\Jobs\Quote\CreateQuotePdf;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Design;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use App\Repositories\DesignRepository;
use App\Requests\SearchRequest;
use App\Search\DesignSearch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Description of InvoiceTest
 *
 * @author michael.hampton
 */
class DesignTest extends TestCase
{

    use DatabaseTransactions,
        WithFaker;

    private $customer;
    private $account;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->account = Account::find(1);
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();
    }

    /** @test */
    public function it_can_show_all_the_designs()
    {
        Design::factory()->create();
        $list = (new DesignSearch(new DesignRepository(new Design())))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_update_the_design()
    {
        $design = Design::factory()->create();
        $name = $this->faker->firstName;
        $data = ['name' => $name];
        $updated = $design->update($data);
        $designRepo = new DesignRepository(new Design);
        $found = $designRepo->findDesignById($design->id);
        $this->assertTrue($updated);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
    public function it_can_show_the_design()
    {
        $design = Design::factory()->create();
        $designRepo = new DesignRepository(new Design());
        $found = $designRepo->findDesignById($design->id);
        $this->assertInstanceOf(Design::class, $found);
        $this->assertEquals($design->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_design()
    {
        $user = User::factory()->create();
        $design = (new DesignFactory)->create(1, $user->id);

        $name = $this->faker->firstName;


        $data = [
            'name'       => $name,
            'user_id'    => $user->id,
            'account_id' => 1,
            'design'     => 'test'
        ];

        $designRepo = new DesignRepository(new Design);
        $design->fill($data);
        $saved = $design->save();
        $this->assertTrue($saved);
        $this->assertEquals($data['name'], $design->name);
    }

    public function testQuoteDesignExists()
    {
        $this->quote = Quote::factory()->create(
            [
                'user_id'     => $this->user->id,
                'customer_id' => $this->customer->id,
                'company_id'  => $this->account->id,
            ]
        );

        $this->contact = $this->quote->customer->primary_contact()->first();

        $design = Design::find(3);

        $html = (new GenerateHtml())->generateEntityHtml(
            new InvoicePdf($this->quote),
            $design,
            $this->quote,
            $this->contact,
            'quote'
        );

        $this->assertStringContainsString('<body>', $html);

        $this->quote->uses_inclusive_taxes = false;

        $settings = $this->quote->customer->settings;
        $settings->invoice_design_id = "VolejRejNm";

        $this->customer->settings = $settings;
        $this->customer->save();

        (new GeneratePdf($this->quote))->execute();
    }

    public function testInvoiceDesignExists()
    {
        $this->invoice = Invoice::factory()->create(
            [
                'user_id'     => $this->user->id,
                'customer_id' => Customer::first(),
                'company_id'  => $this->account->id,
            ]
        );

        $this->contact = $this->invoice->customer->primary_contact()->first();

        $design = Design::find(3);

        $html = (new GenerateHtml())->generateEntityHtml(
            new InvoicePdf($this->invoice),
            $design,
            $this->invoice,
            $this->contact,
            'quote'
        );

        $this->assertStringContainsString('<body>', $html);

        $this->invoice->uses_inclusive_taxes = false;

        $settings = $this->invoice->customer->settings;

        $settings->invoice_design_id = "VolejRejNm";
        $this->customer->settings = $settings;
        $this->customer->save();

        (new GeneratePdf($this->invoice))->execute();
    }
}
