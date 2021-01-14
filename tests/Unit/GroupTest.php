<?php

namespace Tests\Unit;

use App\Factory\GroupFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Requests\SearchRequest;
use App\Search\GroupSearch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GroupTest extends TestCase
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
    public function it_can_show_all_the_groups()
    {
        Group::factory()->create();
        $list = (new GroupSearch(new GroupRepository(new Group)))->filter(
            new SearchRequest,
            $this->account
        );
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_delete_the_group()
    {
        $group = Group::factory()->create();
        $deleted = $group->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_group()
    {
        $group = Group::factory()->create();
        $deleted = $group->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_group()
    {
        $group_setting = Group::factory()->create();
        $data = ['name' => $this->faker->word()];
        $group_setting_repo = new GroupRepository($group_setting);
        $updated = $group_setting_repo->save($data, $group_setting);
        $found = $group_setting_repo->findGroupById($group_setting->id);
        $this->assertInstanceOf(Group::class, $updated);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
    public function it_can_show_the_group()
    {
        $group_setting = Group::factory()->create();
        $group_setting_repo = new GroupRepository(new Group);
        $found = $group_setting_repo->findGroupById($group_setting->id);
        $this->assertInstanceOf(Group::class, $found);
        $this->assertEquals($group_setting->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_group()
    {
        $user = User::factory()->create();
        $factory = (new GroupFactory())->create($this->account, $user);


        $data = [
            'account_id' => $this->account->id,
            'user_id'    => $user->id,
            'name'       => $this->faker->word()
        ];

        $group_setting_repo = new GroupRepository(new Group);
        $group_setting = $group_setting_repo->save($data, $factory);
        $this->assertInstanceOf(Group::class, $group_setting);
        $this->assertEquals($data['name'], $group_setting->name);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
