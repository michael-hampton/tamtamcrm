<?php

namespace Tests\Unit;

use App\Designs\Custom;
use App\Factory\CustomerGatewayTokenFactory;
use App\Factory\GroupFactory;
use App\Models\Account;
use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\CustomerGatewayToken;
use App\Models\Group;
use App\Models\User;
use App\Repositories\CustomerGatewayTokenRepository;
use App\Repositories\GroupRepository;
use App\Requests\SearchRequest;
use App\Search\CustomerGatewayTokenSearch;
use App\Search\GroupSearch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

class CustomerGatewayTokenTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var User|Collection|Model|mixed
     */
    private User $user;

    private $customer;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
    }


    /** @test */
    public function it_can_show_all_the_tokens()
    {
        CustomerGatewayToken::factory()->create();

        $list = (new CustomerGatewayTokenSearch(new CustomerGatewayTokenRepository(new CustomerGatewayToken())))->filter(
            new SearchRequest,
            $this->account
        );
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_delete_the_token()
    {
        $token = CustomerGatewayToken::factory()->create();
        $deleted = $token->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_token()
    {
        $token = CustomerGatewayToken::factory()->create();
        $deleted = $token->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_token()
    {
        $token = CustomerGatewayToken::factory()->create();
        $company_gateway_id = CompanyGateway::first();
        $data = ['company_gateway_id' => $company_gateway_id->id];
        $token_repo = new CustomerGatewayTokenRepository($token);
        $updated = $token_repo->update($data, $token);
        $found = $token_repo->findCustomerGatewayTokenById($token->id);
        $this->assertInstanceOf(CustomerGatewayToken::class, $updated);
        $this->assertEquals($data['company_gateway_id'], $found->company_gateway_id);
    }

    /** @test */
    public function it_can_show_the_token()
    {
        $token = CustomerGatewayToken::factory()->create();
        $token_repo = new CustomerGatewayTokenRepository(new CustomerGatewayToken());
        $found = $token_repo->findCustomerGatewayTokenById($token->id);
        $this->assertInstanceOf(CustomerGatewayToken::class, $found);
        $this->assertEquals($token->company_gateway_id, $found->company_gateway_id);
    }

    /** @test */
    public function it_can_create_a_token()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $company_gateway_id = CompanyGateway::first();
        $factory = (new CustomerGatewayTokenFactory())->create($this->account, $user, $customer);

        $data = [
            'customer_id'        => $customer->id,
            'account_id'         => $this->account->id,
            'user_id'            => $user->id,
            'company_gateway_id' => $company_gateway_id->id,
            'token'              => $this->faker->password(10)
        ];

        $token_repo = new CustomerGatewayTokenRepository(new CustomerGatewayToken());
        $token = $token_repo->create($data, $factory);
        $this->assertInstanceOf(CustomerGatewayToken::class, $token);
        $this->assertEquals($data['company_gateway_id'], $token->company_gateway_id);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
