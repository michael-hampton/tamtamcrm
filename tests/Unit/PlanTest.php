<?php

namespace Tests\Unit;

use App\Services\Account\CreateAccount;
use App\Services\Invoice\CreatePayment;
use App\Components\Promocodes\Promocodes;
use App\Jobs\ProcessSubscription;
use App\Models\Account;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\DomainRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
    }

    public function test_it_creates_plan_on_setup()
    {
        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'password' => $this->faker->word,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
        ];

        $account = (new CreateAccount())->execute($data);
        $domain = $account->domain;

        $subscription_plans = $domain->plans;

        $this->assertNotNull($subscription_plans);

        $this->assertEquals(1, $subscription_plans->count());

        $subscription_plan = $subscription_plans->first();

        $plan = $subscription_plan->plan;

        $this->assertEquals('month', $plan->invoice_interval);
        $this->assertEquals('STDM', $plan->code);
        $this->assertEquals(now()->addYearNoOverflow()->format('Y-m-d'), $subscription_plan->ends_at->format('Y-m-d'));
        $this->assertEquals(
            now()->addMonthNoOverflow()->format('Y-m-d'),
            $subscription_plan->due_date->format('Y-m-d')
        );
        $this->assertEquals(now()->format('Y-m-d'), $subscription_plan->starts_at->format('Y-m-d'));
    }

    public function test_renewal()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions->first();

        $subscription->ends_at = now();
        $subscription->save();

        $subscription->renew();

        $subscription = $subscription->fresh();

        $this->assertEquals(now()->addYearNoOverflow()->format('Y-m-d'), $subscription->ends_at->format('Y-m-d'));
    }

    public function test_upgrade_trial()
    {
        $account = Account::factory()->create();
        $customer = Customer::factory()->create();

        $domain = $account->domains;

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDMT')->first();

        $trial_ends = now()->addDays($plan->trial_period);

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions->first();

        $expected_due_date = $subscription->starts_at->addMonth();

        $this->assertEquals($trial_ends->format('Y-m-d'), $subscription->trial_ends_at->format('Y-m-d'));

        $this->assertEquals(
            $subscription->trial_ends_at->format('Y-m-d'),
            now()->addDays($subscription->plan->trial_period)->format('Y-m-d')
        );

        //$this->assertEquals(99999, $plan->number_of_licences);
        $this->assertEquals(
            $expected_due_date->format('Y-m-d'),
            $subscription->due_date->format('Y-m-d')
        );

        // 1 month plus length of trial
        $this->assertEquals(
            $subscription->trial_ends_at->addMonthNoOverflow()->format('Y-m-d'),
            $subscription->ends_at->format('Y-m-d')
        );
    }

    public function test_upgrade()
    {
        $account = Account::factory()->create();
        $customer = Customer::factory()->create();

        $domain = $account->domains;

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions->first();

        $this->assertEquals(
            now()->addMonthNoOverflow()->format('Y-m-d'),
            Carbon::parse($subscription->due_date)->format('Y-m-d')
        );

        $this->assertEquals(
            now()->addYearNoOverflow()->format('Y-m-d'),
            Carbon::parse($subscription->ends_at)->format('Y-m-d')
        );

        // Change subscription plan
        $plan = Plan::where('code', '=', 'PROY')->first();
        $subscription->changePlan($plan);

        $subscription = $subscription->fresh();

        $this->assertEquals($subscription->due_date->format('Y-m-d'), now()->addYearNoOverflow()->format('Y-m-d'));

        $this->assertEquals($subscription->plan->code, 'PROY');

        $this->assertEquals(
            Carbon::now()->addYearNoOverflow()->format('Y-m-d'),
            $subscription->ends_at->format('Y-m-d')
        );
    }

    public function test_send_subscription()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions->first();

        $subscription->due_date = now()->addDays(10);
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 10;
        $subscription->save();

        ProcessSubscription::dispatchNow();

        $cost = $subscription->plan->price * $subscription->number_of_licences;

        $this->assertDatabaseHas(
            'invoices',
            [
                'customer_id' => $customer->id,
                'balance' => $cost,
                //'due_date'    => $domain->subscription_expiry_date
            ]
        );
    }

    public function test_expires()
    {
        $customer = Customer::find(5);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'PROM')->first();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions()->latest()->first();

        $subscription->due_date = now()->addDays(10);
        $subscription->ends_at = now();
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 10;
        $subscription->save();

        ProcessSubscription::dispatchNow();

        $subscription = $subscription->fresh();

        $this->assertEquals($subscription->ends_at->format('Y-m-d'), now()->addYearNoOverflow()->format('Y-m-d'));
    }

    public function test_autobill()
    {
        $customer = Customer::find(5);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'PROM')->first();
        $plan->auto_billing_enabled = true;
        $plan->save();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions()->latest()->first();

        $subscription->due_date = now()->addDays(10);
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 10;
        $subscription->save();

        ProcessSubscription::dispatchNow();

        $cost = $subscription->plan->price * $subscription->number_of_licences;

        $invoice = Invoice::where('plan_subscription_id', '=', $subscription->id)->first();

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($invoice->payments->count(), 1);
    }

    public function test_subscription_with_promocode()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $domain->default_account_id = $this->account->id;
        $domain->save();

        $promocode = (new Promocodes)->createDisposable($this->account, 1, 10, [], Carbon::now()->addDays(10), 1);

        $customer->newSubscription('main', $plan, $this->account);

        $subscription = $customer->subscriptions->first();

        $subscription->due_date = now()->addDays(10);
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 10;
        $subscription->promocode = $promocode->first()['code'];

        $subscription->save();

        $cost = $subscription->plan->price * $subscription->number_of_licences - $promocode->first()['reward'];

        ProcessSubscription::dispatchNow();

        //$invoice = Invoice::where('customer_id', '=', $customer->id)->first();

        $this->assertDatabaseHas(
            'invoices',
            [
                'customer_id' => $customer->id,
                'balance' => number_format($cost, 4),
                //'due_date'    => $domain->subscription_expiry_date
            ]
        );

        $subscription = $subscription->fresh();

        //$this->assertEquals($subscription->promocode_applied, true);
        $this->assertEquals($subscription->promocode, $promocode->first()['code']);
    }

    public function test_refund_subscription()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $domain->default_account_id = $this->account->id;
        $domain->save();

        $customer->newSubscription('main', $plan, $this->account);

        $subscription = $customer->subscriptions->first();
        $subscription->starts_at = Carbon::now()->subMonthNoOverflow();
        $subscription->trial_ends_at = Carbon::now()->subMonths(2);
        $subscription->api_key = 'test123';
        $subscription->webhook_url = 'http://taskman2.develop/api/invoice';
        $subscription->due_date = now()->addDays(10);
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 1;
        $subscription->save();

        ProcessSubscription::dispatchNow();

        $original_invoice = Invoice::subscriptions($subscription)->first();

        (new CreatePayment($original_invoice, new InvoiceRepository(new Invoice()),
            new PaymentRepository(new Payment())))->execute();

        $subscription->cancel(true);

        $invoice = $original_invoice->fresh();
        $payment = $invoice->payments->first()->fresh();

        $this->assertEquals($invoice->balance, $original_invoice->total);
        $this->assertEquals($payment->refunded, $invoice->total);
        $this->assertEquals($payment->status_id, 6);
    }

    public function test_change_plan()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id' => $plan->id
            ]
        );

        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions->first();

        $subscription->due_date = now()->addDays(10);
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 1;
        $subscription->save();

        ProcessSubscription::dispatchNow();

        $cost = $subscription->plan->price * $subscription->number_of_licences;

        $invoice = Invoice::where('customer_id', '=', $customer->id)->where('balance', '=', $cost)->first();

        $invoice->date = now()->subMonthsNoOverflow(3);
        //$invoice->balance = 0;
        $invoice->save();

        $new_plan = Plan::where('code', '=', 'PROY')->first();
        $subscription->changePlan($new_plan);

        $subscription->due_date = now()->addDays(10);
        $subscription->trial_ends_at = null;
        $subscription->number_of_licences = 1;
        $subscription->save();

        ProcessSubscription::dispatchNow();

        $credit = Credit::where('plan_subscription_id', '=', $subscription->id)->get();

        $this->assertEquals($credit->count(), 1);

        $subscription = $subscription->fresh();

        $this->assertEquals($subscription->amount_owing, 0);
    }
}