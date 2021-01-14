<?php

namespace Tests\Unit;

use App\Factory\CaseFactory;
use App\Jobs\Cases\ProcessOverdueCases;
use App\Mail\TestMail;
use App\Models\Account;
use App\Models\Cases;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\CaseRepository;
use App\Requests\SearchRequest;
use App\Search\CaseSearch;
use App\Transformations\TaskTransformable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CaseTest extends TestCase
{

    use DatabaseTransactions, WithFaker, TaskTransformable;

    /**
     * @var User|Collection|Model|mixed
     */
    private User $user;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var Customer|Collection|Model|mixed
     */
    private Customer $customer;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
        config(['mail.driver' => 'log']);
    }

    /** @test */
    public function it_can_show_all_the_cases()
    {
        Cases::factory()->create();
        $list = (new CaseSearch(new CaseRepository(new Cases())))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
        // $this->assertInstanceOf(Collection::class, $list);
        //$this->assertEquals($insertedtask->title, $myLastElement['title']);
    }

    /** @test */
    public function it_can_delete_the_case()
    {
        $case = Cases::factory()->create();
        $deleted = $case->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_case()
    {
        $case = Cases::factory()->create();
        $deleted = $case->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_case()
    {
        $case = Cases::factory()->create();
        $data = ['message' => $this->faker->sentence, 'status_id' => 2];
        $caseRepo = new CaseRepository($case);
        $case = $caseRepo->updateCase($data, $case, $case->user);
        $this->assertInstanceOf(Cases::class, $case);
        $this->assertEquals($data['message'], $case->message);
        $this->assertEquals($case->status_id, $data['status_id']);
        $this->assertNotEmpty($case->date_opened);
    }

    /** @test */
    public function it_can_show_the_case()
    {
        $case = Cases::factory()->create();
        $caseRepo = new CaseRepository(new Cases);
        $found = $caseRepo->findCaseById($case->id);
        $this->assertInstanceOf(Cases::class, $found);
        $this->assertEquals($case->subject, $found->subject);
    }


    /** @test */
    public function it_can_create_a_case()
    {
        $data = [
            'account_id'  => $this->account->id,
            'user_id'     => $this->user->id,
            'customer_id' => $this->customer->id,
            'subject'     => $this->faker->word,
            'message'     => $this->faker->sentence
        ];

        $caseRepo = new CaseRepository(new Cases);
        $factory = (new CaseFactory)->create($this->account, $this->user, $this->customer);
        $case = $caseRepo->createCase($data, $factory);

        $this->assertInstanceOf(Cases::class, $case);
        $this->assertEquals($data['message'], $case->message);
    }

    /** @test */
    public function incoming_mail_is_saved_to_the_cases_table()
    {
        // Given: we have an e-mail﻿
        $email = new TestMail(
            $sender = 'michaelhamptondesign@yahoo.com',
            $subject = 'Test E-mail',
            $body = 'Some example text in the body'
        );

        $user = User::whereId(5)->first();

        Auth::login($user);

        // When: we receive that e-mail
        //Mail::to('michael.hampton_cases+0003@tamtamcrm.com')->send($email);
        Mail::to('michael.hampton_cases@tamtamcrm.com')->send($email);

        // Then: we assert the e-mails (meta)data was stored
        $case = Cases::whereSubject($subject)->first();
        $this->assertInstanceOf(Cases::class, $case);
    }

    public function test_overdue_case()
    {
        $data = [
            'account_id'  => $this->account->id,
            'user_id'     => 5,
            'customer_id' => $this->customer->id,
            'subject'     => $this->faker->word,
            'message'     => $this->faker->sentence,
            'due_date'    => Carbon::tomorrow(),
            'status_id' => Cases::STATUS_OPEN
        ];

        $caseRepo = new CaseRepository(new Cases);
        $factory = (new CaseFactory)->create($this->account, $this->user, $this->customer);
        $case = $caseRepo->createCase($data, $factory);

        ProcessOverdueCases::dispatchNow(new CaseRepository(new Cases()));

        $this->assertInstanceOf(Cases::class, $case);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
