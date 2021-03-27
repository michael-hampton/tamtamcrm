<?php

namespace Tests\Unit;

use App\Actions\Account\CreateAccount;
use App\Components\Promocodes\Promocodes;
use App\Jobs\ProcessSubscription;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Domain;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\DomainRepository;
use Carbon\Carbon;
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
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'email'       => $this->faker->safeEmail,
            'password'    => $this->faker->word,
            'customer_id' => $this->customer->id,
            'user_id'     => $this->user->id,
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
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id'       => $plan->id
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
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id'       => $plan->id
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
                'balance'     => $cost,
                //'due_date'    => $domain->subscription_expiry_date
            ]
        );
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
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'support_email' => $this->faker->safeEmail,
                'plan_id'       => $plan->id
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
                'balance'     => number_format($cost, 4),
                //'due_date'    => $domain->subscription_expiry_date
            ]
        );

        $subscription = $subscription->fresh();

        //$this->assertEquals($subscription->promocode_applied, true);
        $this->assertEquals($subscription->promocode, $promocode->first()['code']);
    }
}