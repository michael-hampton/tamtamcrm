<?php

namespace Tests\Unit;

use App\Factory\BankAccountFactory;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Cases;
use App\Models\Customer;
use App\Models\BankAccount;
use App\Models\User;
use App\Repositories\BankAccountRepository;
use App\Requests\SearchRequest;
use App\Search\BankAccountSearch;
use App\Search\DealSearch;
use App\Transformations\DealTransformable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

class BankAccountTest extends TestCase
{

    use DatabaseTransactions, WithFaker, DealTransformable;

    private $user;
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
    }

    /** @test */
    public function it_can_show_all_the_bank_accounts()
    {
        BankAccount::factory()->create();

        $list = (new BankAccountSearch(
            new BankAccountRepository(
                new BankAccount
            )
        ))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
        //$this->assertInstanceOf(BankAccount::class, $list[0]);
        // $this->assertInstanceOf(Collection::class, $list);
        //$this->assertEquals($insertedbank_account->name, $myLastElement['name']);
    }

    /** @test */
    public function it_can_delete_the_bank_account()
    {
        $bank_account = BankAccount::factory()->create();
        $deleted = $bank_account->deleteEntity();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_archive_the_bank_account()
    {
        $bank_account = BankAccount::factory()->create();
        $deleted = $bank_account->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_bank_account()
    {
        $bank_account = BankAccount::factory()->create();
        $data = ['username' => $this->faker->userName, 'password' => $this->faker->password];
        $bank_accountRepo = new BankAccountRepository($bank_account);
        $bank_account = $bank_accountRepo->update($data, $bank_account);
        $this->assertInstanceOf(BankAccount::class, $bank_account);
        $this->assertTrue(Hash::check($data['password'], $bank_account->password));
        $this->assertEquals($data['username'], $bank_account->username);
    }

    /** @test */
    public function it_can_show_the_bank_account()
    {
        $bank_account = BankAccount::factory()->create();
        $bank_accountRepo = new BankAccountRepository(new BankAccount);
        $found = $bank_accountRepo->findBankAccountById($bank_account->id);
        $this->assertInstanceOf(BankAccount::class, $found);
        $this->assertEquals($bank_account->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_bank_account()
    {
        $data = [
            'account_id'    => $this->account->id,
            'bank_id'       => Bank::first()->id,
            'username'      => $this->faker->userName,
            'password'      => $this->faker->password,
            'customer_note'  => $this->faker->paragraph,
            'internal_note' => $this->faker->paragraph
        ];

        $bank_accountRepo = new BankAccountRepository(new BankAccount);
        $factory = (new BankAccountFactory())->create($this->account, $this->user);
        $bank_account = $bank_accountRepo->create($data, $factory);

        $this->assertInstanceOf(BankAccount::class, $bank_account);
        $this->assertTrue(Hash::check($data['password'], $bank_account->password));
        $this->assertEquals($data['username'], $bank_account->username);
        $this->assertEquals($data['bank_id'], $bank_account->bank_id);
    }


    /** @test */
    public function it_errors_finding_a_bank_account()
    {
        $this->expectException(ModelNotFoundException::class);
        $bank_account = new BankAccountRepository(new BankAccount);
        $bank_account->findBankAccountById(999);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
