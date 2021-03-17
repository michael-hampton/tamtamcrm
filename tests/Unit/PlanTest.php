<?php

namespace Tests\Unit;

use App\Actions\Account\CreateAccount;
use App\Actions\Plan\CreatePlan;
use App\Actions\Plan\UpgradePlan;
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

        $plans = $domain->plans;

        $this->assertNotNull($plans);
        $this->assertEquals(1, $plans->count());

        $plan = $plans->first();

        $this->assertEquals('MONTHLY', $plan->plan_period);
        $this->assertEquals('STANDARD', $plan->plan);
        $this->assertEquals(now()->addYearNoOverflow()->format('Y-m-d'), $plan->expiry_date);
        $this->assertEquals(now()->addMonthNoOverflow()->format('Y-m-d'), $plan->due_date);
        $this->assertEquals(now()->format('Y-m-d'), $plan->plan_started);
    }

    public function test_upgrade_trial()
    {
        $data = [
            'plan_period' => 'TRIAL',
            'plan'        => 'TRIAL'
        ];

        $plan = (new CreatePlan())->execute($this->account->domains, $data);

        $this->assertEquals(99999, $plan->number_of_licences);
        $this->assertEquals(
            now()->addMonthNoOverflow()->format('Y-m-d'),
            Carbon::parse($plan->due_date)->format('Y-m-d')
        );
        $this->assertEquals(
            now()->addMonthNoOverflow()->format('Y-m-d'),
            Carbon::parse($plan->expiry_date)->format('Y-m-d')
        );

        $plan->due_date = now()->addDays(10);
        $plan->expiry_date = now();
        $plan->save();

        ProcessSubscription::dispatchNow();

        $domain = $plan->domain->fresh();

        $this->assertEquals($domain->plans->count(), 2);

        $first_plan = $domain->plans->first();
        $latest_plan = $domain->plans->last();

        $this->assertEquals(now()->format('Y-m-d'), $first_plan->plan_ended);
        $this->assertEquals(false, $first_plan->is_active);

        $this->assertEquals(now()->format('Y-m-d'), $latest_plan->plan_started);
        $this->assertEquals('MONTHLY', $latest_plan->plan_period);
        $this->assertEquals('STANDARD', $latest_plan->plan);
    }

    public function test_upgrade()
    {
        $data = [
            'plan_period' => 'MONTHLY',
            'plan'        => 'STANDARD'
        ];

        $plan = (new CreatePlan())->execute($this->account->domains, $data);

        $this->assertEquals(
            now()->addMonthNoOverflow()->format('Y-m-d'),
            Carbon::parse($plan->due_date)->format('Y-m-d')
        );
        $this->assertEquals(
            now()->addYearNoOverflow()->format('Y-m-d'),
            Carbon::parse($plan->expiry_date)->format('Y-m-d')
        );

        (new UpgradePlan())->execute($plan->domain, ['plan' => 'ADVANCED', 'plan_period' => 'MONTHLY']);

        $domain = $plan->domain->fresh();

        $this->assertEquals($domain->plans->count(), 2);

        $first_plan = $domain->plans->first();
        $latest_plan = $domain->plans->last();

        $this->assertEquals(now()->format('Y-m-d'), $first_plan->plan_ended);
        $this->assertEquals(false, $first_plan->is_active);

        $this->assertEquals(now()->format('Y-m-d'), $latest_plan->plan_started);
        $this->assertEquals('MONTHLY', $latest_plan->plan_period);
        $this->assertEquals('ADVANCED', $latest_plan->plan);
    }

    public function test_send_subscription()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'support_email' => $this->faker->safeEmail
            ]
        );

        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        $plan = (new CreatePlan())->execute(
            $domain,
            [
                'plan_period'        => Plan::PLAN_PERIOD_MONTH,
                'plan'               => Plan::PLAN_STANDARD,
                'number_of_licences' => 10,
            ]
        );

        $plan->due_date = now()->addDays(10);
        $plan->save();

        if ($plan->plan === Plan::PLAN_STANDARD) {
            $cost = $plan->plan_period === Plan::PLAN_PERIOD_YEAR ? env(
                'STANDARD_YEARLY_ACCOUNT_PRICE'
            ) : env('STANDARD_MONTHLY_ACCOUNT_PRICE');
        } else {
            $cost = $plan->plan_period === Plan::PLAN_PERIOD_YEAR ? env(
                'ADVANCED_YEARLY_ACCOUNT_PRICE'
            ) : env('ADVANCED_MONTHLY_ACCOUNT_PRICE');
        }

        $number_of_licences = $plan->number_of_licences;

        if ($number_of_licences > 1 && $number_of_licences !== 99999) {
            $cost *= $number_of_licences;
        }

        ProcessSubscription::dispatchNow();

        $this->assertDatabaseHas(
            'invoices',
            [
                'customer_id' => $customer->id,
                'balance'     => $cost,
                //'due_date'    => $domain->subscription_expiry_date
            ]
        );
    }

    public function test_send_subscription_with_promocode()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'support_email' => $this->faker->safeEmail
            ]
        );

        $domain->default_account_id = $this->account->id;
        $domain->save();

        $promocode = (new Promocodes)->createDisposable($this->account, 1, 10, [], Carbon::now()->addDays(10), 1);

        $plan = (new CreatePlan())->execute(
            $domain,
            [
                'promocode'          => $promocode->first()['code'],
                'plan_period'        => Plan::PLAN_PERIOD_MONTH,
                'plan'               => Plan::PLAN_STANDARD,
                'number_of_licences' => 10,
            ]
        );

        $plan->due_date = now()->addDays(10);
        $plan->save();

        $cost = env('STANDARD_MONTHLY_ACCOUNT_PRICE') * 10 - 10;

        ProcessSubscription::dispatchNow();

        //$invoice = Invoice::where('customer_id', '=', $customer->id)->where('balance', '=', $cost)->first();

        $this->assertDatabaseHas(
            'invoices',
            [
                'customer_id' => $customer->id,
                'balance'     => number_format($cost, 4),
                //'due_date'    => $domain->subscription_expiry_date
            ]
        );

        $plan = $plan->fresh();

        $this->assertEquals($plan->promocode_applied, true);
        $this->assertEquals($plan->promocode, $promocode->first()['code']);
    }
}