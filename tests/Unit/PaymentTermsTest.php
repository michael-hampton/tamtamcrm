<?php

namespace Tests\Unit;

use App\Factory\PaymentTermsFactory;
use App\Models\Account;
use App\Models\PaymentTerms;
use App\Models\User;
use App\Repositories\PaymentTermsRepository;
use App\Requests\SearchRequest;
use App\Search\PaymentTermsSearch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentTermsTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var User
     */
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
    }


    /** @test */
    public function it_can_show_all_the_terms()
    {
        PaymentTerms::factory()->create();
        $list = (new PaymentTermsSearch(new PaymentTermsRepository(new PaymentTerms)))->filter(
            new SearchRequest,
            $this->account
        );
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_delete_the_term()
    {
        $payment_term = PaymentTerms::factory()->create();
        $deleted = $payment_term->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_term()
    {
        $payment_term = PaymentTerms::factory()->create();
        $deleted = $payment_term->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_term()
    {
        $payment_term = PaymentTerms::factory()->create();
        $data = ['name' => $this->faker->word()];
        $payment_terms_repo = new PaymentTermsRepository($payment_term);
        $updated = $payment_terms_repo->save($data, $payment_term);
        $found = $payment_terms_repo->findPaymentTermsById($payment_term->id);
        $this->assertInstanceOf(PaymentTerms::class, $updated);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
    public function it_can_show_the_term()
    {
        $payment_term = PaymentTerms::factory()->create();
        $payment_terms_repo = new PaymentTermsRepository(new PaymentTerms);
        $found = $payment_terms_repo->findPaymentTermsById($payment_term->id);
        $this->assertInstanceOf(PaymentTerms::class, $found);
        $this->assertEquals($payment_term->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_term()
    {
        $user = User::factory()->create();
        $factory = (new PaymentTermsFactory)->create($this->account, $user);

        $data = [
            'account_id' => $this->account->id,
            'user_id'    => $user->id,
            'name'       => $this->faker->word()
        ];

        $payment_terms_repo = new PaymentTermsRepository(new PaymentTerms);
        $payment_term = $payment_terms_repo->save($data, $factory);
        $this->assertInstanceOf(PaymentTerms::class, $payment_term);
        $this->assertEquals($data['name'], $payment_term->name);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
