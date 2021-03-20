<?php

namespace Tests\Unit;

use App\Actions\Account\ConvertAccount;
use App\Actions\Account\CreateAccount;
use App\Jobs\ProcessSubscription;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\DomainRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_can_convert_the_account()
    {
        $account = Account::factory()->create();
        $account = (new ConvertAccount($account))->execute();

        $this->assertInstanceOf(Account::class, $account);
        $this->assertInstanceOf(Customer::class, $account->domains->customer);
        $this->assertInstanceOf(User::class, $account->domains->user);
        $this->assertEquals(1, $account->domains->customer->contacts->count());
    }

    /** @test */
    public function it_can_create_an_account()
    {
        $account = (new CreateAccount())->execute(
            ['email' => $this->faker->safeEmail, 'password' => $this->faker->password]
        );

        $domain = $account->domain;
        $plan = $domain->plans()->where('is_active', true)->first();
        $this->assertEquals($plan->due_date, now()->addMonthNoOverflow()->format('Y-m-d'));
        $this->assertEquals($plan->plan_period, Plan::PLAN_PERIOD_MONTH);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
