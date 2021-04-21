<?php

namespace Tests\Unit;

use App\Components\Payment\Gateways\Stripe;
use App\Factory\TimerFactory;
use App\Models\Account;
use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\CustomerGateway;
use App\Models\Task;
use App\Models\Timer;
use App\Models\User;
use App\Repositories\TimerRepository;
use App\Transformations\TaskTransformable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StripeTest extends TestCase
{

    use DatabaseTransactions, WithFaker, TaskTransformable;

    private $user;
    private $customer;
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
        $this->task = Task::factory()->create();
    }

    /** @test */
    public function it_can_create_a_stripe_account()
    {
        $customer = Customer::where('id', '=', 5)->first();
        $company_gateway = CompanyGateway::where('id', '=', 5)->first();
        $customer_gateway = CustomerGateway::where('id', '=', 1)->first();

        // bank account ba_1Iibn6RAXgspuYr6Q5Yz7doB
        // connected account acct_1IibAzRAXgspuYr6
        (new Stripe($customer, $customer_gateway, $company_gateway))->retrieveBankAccount('acct_1IibAzRAXgspuYr6', 'ba_1Iibn6RAXgspuYr6Q5Yz7doB');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
