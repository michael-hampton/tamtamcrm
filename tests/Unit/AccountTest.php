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
        $this->assertEquals($domain->subscription_expiry_date, now()->addMonthNoOverflow()->format('Y-m-d'));
        $this->assertEquals($domain->subscription_period, Domain::SUBSCRIPTION_PERIOD_MONTH);
    }

    public function test_send_subscription()
    {
        $customer = Customer::factory()->create();
        $contact = CustomerContact::factory()->create(['customer_id' => $customer->id]);
        $customer->contacts()->save($contact);
        $user = User::factory()->create();

        $domain = (new DomainRepository(new Domain))->create(
            [
                'user_id'                  => $user->id,
                'customer_id'              => $customer->id,
                'subscription_expiry_date' => now()->addDays(10),
                'subscription_period'      => Domain::SUBSCRIPTION_PERIOD_MONTH,
                'subscription_plan'        => Domain::SUBSCRIPTION_STANDARD,
                'number_of_licences'       => 10,
                'support_email'            => $this->faker->safeEmail
            ]
        );
        $account = Account::factory()->create(['domain_id' => $domain->id, 'support_email' => $this->faker->safeEmail]);
        $domain->default_account_id = $account->id;
        $domain->save();

        if ($domain->subscription_plan === Domain::SUBSCRIPTION_STANDARD) {
            $cost = $domain->subscription_period === Domain::SUBSCRIPTION_PERIOD_YEAR ? env(
                'STANDARD_YEARLY_ACCOUNT_PRICE'
            ) : env('STANDARD_MONTHLY_ACCOUNT_PRICE');
        } else {
            $cost = $domain->subscription_period === Domain::SUBSCRIPTION_PERIOD_YEAR ? env(
                'ADVANCED_YEARLY_ACCOUNT_PRICE'
            ) : env('ADVANCED_MONTHLY_ACCOUNT_PRICE');
        }

        $number_of_licences = $domain->number_of_licences;

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

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
