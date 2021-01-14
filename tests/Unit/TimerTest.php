<?php

namespace Tests\Unit;

use App\Factory\TimerFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Task;
use App\Models\Timer;
use App\Models\User;
use App\Repositories\TimerRepository;
use App\Transformations\TaskTransformable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimerTest extends TestCase
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
    public function it_can_create_a_timer()
    {
        $data = [
            'date'       => Carbon::now()->format('Y-m-d'),
            'start_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'end_time'   => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
        ];

        $timerRepo = new TimerRepository(new Timer());
        $factory = (new TimerFactory())->create($this->user, $this->account, $this->task);
        $timer = $timerRepo->save($this->task, $factory, $data);

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertEquals($data['start_time'], $timer->started_at);
        $this->assertEquals($data['start_time'], $timer->stopped_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_update_a_timer()
    {
        $data = [
            'date'       => Carbon::now()->format('Y-m-d'),
            'start_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'end_time'   => null,
        ];

        $timerRepo = new TimerRepository(new Timer());
        $factory = (new TimerFactory())->create($this->user, $this->account, $this->task);
        $timer = $timerRepo->save($this->task, $factory, $data);

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertEquals($data['start_time'], $timer->started_at);
        $this->assertEmpty($timer->stopped_at);

        $is_running = $timerRepo->isRunning($this->task);
        $this->assertTrue($is_running);

        $start_time = $timerRepo->getStartTime($this->task);
        $this->assertEquals($start_time, $data['start_time']);

        $end_time = Carbon::now()->format('Y-m-d H:i:s');

        $timer = $timerRepo->stopTimer($this->task);
        $this->assertEquals($timer->stopped_at->format('Y-m-d H:i:s'), $end_time);

        $count = $this->task->timers->count();

        $timer = TimerFactory::create($this->user, $this->account, $this->task);
        $timer = $timerRepo->startTimer($timer, $this->task);

        $task = $this->task->fresh();

        $this->assertEquals(($count + 1), $task->timers->count());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
