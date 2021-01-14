<?php

namespace Tests\Unit;

use App\Factory\RecurringQuoteFactory;
use App\Jobs\Invoice\SendRecurringInvoice;
use App\Jobs\Quote\SendRecurringQuote;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\RecurringInvoice;
use App\Models\RecurringQuote;
use App\Models\User;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\RecurringQuoteRepository;
use App\Requests\SearchRequest;
use App\Search\RecurringQuoteSearch;
use App\Transformations\TaskTransformable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecurringQuoteTest extends TestCase
{

    use DatabaseTransactions, WithFaker, TaskTransformable;

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
        $contact = CustomerContact::factory()->create(['customer_id' => $this->customer->id]);
        $this->customer->contacts()->save($contact);
    }

    /** @test */
    public function it_can_show_all_the_quotes()
    {
        RecurringQuote::factory()->create();
        $list = (new RecurringQuoteSearch(new RecurringQuoteRepository(new RecurringQuote())))->filter(
            new SearchRequest(),
            $this->account
        );
        $this->assertNotEmpty($list);
        // $this->assertInstanceOf(Collection::class, $list);
        //$this->assertEquals($insertedtask->title, $myLastElement['title']);
    }

    /** @test */
    public function it_can_delete_the_quote()
    {
        $recurring_quote = RecurringQuote::factory()->create();
        $deleted = $recurring_quote->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_recurring_quote()
    {
        $recurring_quote = RecurringQuote::factory()->create();
        $deleted = $recurring_quote->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_recurring_quote()
    {
        $recurring_quote = RecurringQuote::factory()->create();
        $data = ['due_date' => Carbon::today()->addDays(5)];
        $recurringQuoteRepo = new RecurringQuoteRepository($recurring_quote);
        $task = $recurringQuoteRepo->save($data, $recurring_quote);
        $found = $recurringQuoteRepo->findQuoteById($recurring_quote->id);
        $this->assertInstanceOf(RecurringQuote::class, $recurring_quote);
        $this->assertEquals($data['due_date'], $found->due_date);
    }

    /** @test */
    public function it_can_show_the_quote()
    {
        $recurring_quote = RecurringQuote::factory()->create();
        $recurringQuoteRepo = new RecurringQuoteRepository(new RecurringQuote());
        $found = $recurringQuoteRepo->findQuoteById($recurring_quote->id);
        $this->assertInstanceOf(RecurringQuote::class, $found);
        $this->assertEquals($recurring_quote->due_date, $found->due_date);
    }


    /** @test */
    public function it_can_create_a_quote()
    {
        $data = [
            'account_id'  => $this->account->id,
            'user_id'     => $this->user->id,
            'customer_id' => $this->customer->id,
            'total'       => 200,
            'frequency' => 'MONTHLY'
        ];

        $recurringQuoteRepo = new RecurringQuoteRepository(new RecurringQuote());
        $factory = (new RecurringQuoteFactory())->create($this->customer, $this->account, $this->user, 200);
        $recurring_quote = $recurringQuoteRepo->createQuote($data, $factory);

        $this->assertEquals($recurring_quote->date_to_send, Carbon::today()->addMonthNoOverflow());
        $this->assertInstanceOf(RecurringQuote::class, $recurring_quote);
        $this->assertEquals($data['total'], $recurring_quote->total);
        $this->assertNotEmpty($recurring_quote->invitations);
    }

    public function test_send_recurring_quote()
    {
        $recurring_quote = RecurringQuote::factory()->create();
        $recurring_quote->date_to_send = Carbon::now();
        $recurring_quote->frequency = 'MONTHLY';
        $recurring_quote->customer_id = 5;
        $recurring_quote->date = Carbon::now()->subDays(15);
        $recurring_quote->start_date = Carbon::now()->subDays(1);
        $recurring_quote->expiry_date = Carbon::now()->addDays(15);
        $recurring_quote->auto_billing_enabled = 0;
        $recurring_quote->number_of_occurrances = 2;
        $recurring_quote->status_id = RecurringQuote::STATUS_ACTIVE;
        $recurring_quote->save();

        SendRecurringQuote::dispatchNow(new QuoteRepository(new Quote()));

        $updated_recurring_quote = $recurring_quote->fresh();

        $this->assertTrue(
            $updated_recurring_quote->date_to_send->eq(Carbon::today()->addMonthNoOverflow())
        );
        $this->assertTrue($updated_recurring_quote->last_sent_date->eq(Carbon::today()));
        $this->assertEquals(1, $updated_recurring_quote->number_of_occurrances);
        $quote = Quote::where('recurring_quote_id', $recurring_quote->id)->first();
        $this->assertInstanceOf(Quote::class, $quote);
        $this->assertEquals(Invoice::STATUS_SENT, $quote->status_id);
    }

    public function test_send_recurring_quote_last_cycle()
    {
        $recurring_quote = RecurringQuote::factory()->create();
        $recurring_quote->date_to_send = Carbon::now();
        $recurring_quote->customer_id = 5;
        $recurring_quote->date = Carbon::now()->subDays(15);
        $recurring_quote->start_date = Carbon::now()->subDays(1);
        $recurring_quote->expiry_date = Carbon::now()->addDays(15);
        $recurring_quote->frequency = 'MONTHLY';
        $recurring_quote->auto_billing_enabled = 0;
        $recurring_quote->number_of_occurrances = 1;
        $recurring_quote->status_id = RecurringQuote::STATUS_ACTIVE;
        $recurring_quote->save();

        SendRecurringQuote::dispatchNow(new QuoteRepository(new Quote()));

        $updated_recurring_quote = $recurring_quote->fresh();

        $this->assertNull($updated_recurring_quote->date_to_send);
        $this->assertTrue($updated_recurring_quote->last_sent_date->eq(Carbon::today()));
        $this->assertEquals(0, $updated_recurring_quote->number_of_occurrances);
        $invoice = Quote::where('recurring_quote_id', $recurring_quote->id)->first();
        $this->assertInstanceOf(Quote::class, $invoice);
        $this->assertEquals(Quote::STATUS_SENT, $invoice->status_id);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
