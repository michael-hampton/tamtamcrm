<?php

namespace Tests\Unit;

use App\Components\Promocodes\Promocodes;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Promocode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PromocodeTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * @var User|\Illuminate\Database\Eloquent\Collection|Model|mixed
     */
    private User $user;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * @var Customer|\Illuminate\Database\Eloquent\Collection|Model|mixed
     */
    private Customer $customer;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
        $this->order = Order::factory()->create();
    }

    /** @test */
    public function it_can_create_multiple_promocodes()
    {
        $test = (new Promocodes)->create($this->account, 5, 500, [], Carbon::now()->addDays(10), 8, false);
        $this->assertInstanceOf(Collection::class, $test);
    }

    /** @test */
    public function it_can_create_disposable_promocode()
    {
        $test = (new Promocodes)->createDisposable($this->account, 1, 500, [], Carbon::now()->addDays(10), 8);
        $this->assertInstanceOf(Collection::class, $test);
    }

    /** @test */
    public function it_can_apply_a_discount()
    {
        $promocode = (new Promocodes)->create(
            $this->account,
            1,
            500,
            ['scope' => 'order', 'scope_value' => 10],
            Carbon::now()->addDays(10),
            1,
            false
        );
        $first = $promocode->first();
        $test = (new Promocodes)->apply($this->order, $this->account, $first['code'], $this->customer);
        $this->assertInstanceOf(Promocode::class, $test);
        $this->assertEquals($test->reward, 500);

        $test = (new Promocodes)->apply($this->order, $this->account, $first['code'], $this->customer);
        $this->assertFalse($test);
    }
}