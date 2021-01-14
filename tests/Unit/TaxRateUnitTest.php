<?php

namespace Tests\Unit;

use App\Factory\TaxRateFactory;
use App\Models\TaxRate;
use App\Models\User;
use App\Repositories\TaxRateRepository;
use App\Transformations\EventTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaxRateUnitTest extends TestCase
{

    use DatabaseTransactions, EventTransformable, WithFaker;

    private $user;

    /**
     * @var int
     */
    private $account_id = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_all_the_tax_rates()
    {
        $data = [
            'name' => $this->faker->word,
            'rate' => $this->faker->randomFloat()
        ];

        $factory = (new TaxRateFactory())->create(1, $this->user->id);
        $taxRateRepo = new TaxRateRepository(new TaxRate);
        $taxRateRepo->save($data, $factory);
        $lists = $taxRateRepo->listTaxRates();
        foreach ($lists as $list) {
            $this->assertDatabaseHas('tax_rates', ['name' => $list->name]);
            $this->assertDatabaseHas('tax_rates', ['rate' => $list->rate]);
        }
    }

    /** @test */
    public function it_errors_when_the_tax_rate_is_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $taxRateRepo = new TaxRateRepository(new TaxRate);
        $taxRateRepo->findTaxRateById(999);
    }

    /** @test */
    public function it_can_get_the_tax_rate()
    {
        $data = [
            'name' => $this->faker->word,
            'rate' => $this->faker->randomFloat()
        ];

        $taxRateRepo = new TaxRateRepository(new TaxRate);
        $factory = (new TaxRateFactory())->create(1, $this->user->id);
        $created = $taxRateRepo->save($data, $factory);
        $found = $taxRateRepo->findTaxRateById($created->id);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
//    public function it_errors_updating_the_tax_rate()
//    {
//        $this->expectException(\Illuminate\Database\QueryException::class);
//        $taxRate = TaxRate::factory()->create();
//        $taxRateRepo = new TaxRateRepository($taxRate);
//        $taxRateRepo->updateTaxRate(['name' => null]);
//    }

    /** @test */
    public function it_can_update_the_tax_rate()
    {
        $taxRate = TaxRate::factory()->create();
        $taxRateRepo = new TaxRateRepository($taxRate);
        $update = [
            'account_id' => $this->account_id,
            'name'       => $this->faker->word,
            'rate'       => $this->faker->randomFloat(),
        ];
        $updated = $taxRateRepo->save($update, $taxRate);
        $this->assertInstanceOf(TaxRate::class, $updated);
        $this->assertEquals($update['name'], $taxRate->name);
        $this->assertEquals($update['rate'], $taxRate->rate);
    }

    /** @test */
//    public function it_errors_when_creating_the_tax_rate()
//    {
//        $this->expectException(\Illuminate\Database\QueryException::class);
//        $taxRateRepo = new TaxRateRepository(new TaxRate);
//        $taxRateRepo->createTaxRate([]);
//    }
//
    /** @test */
    public function it_can_create_a_tax_rate()
    {
        $data = [
            'name' => $this->faker->word,
            'rate' => $this->faker->randomFloat(),
        ];
        $taxRateRepo = new TaxRateRepository(new TaxRate);
        $factory = (new TaxRateFactory())->create(1, $this->user->id);
        $created = $taxRateRepo->save($data, $factory);
        $this->assertInstanceOf(TaxRate::class, $created);
        $this->assertEquals($data['name'], $created->name);
    }
}
