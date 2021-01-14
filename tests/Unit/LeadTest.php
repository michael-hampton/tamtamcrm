<?php

namespace Tests\Unit;

use App\Events\Lead\LeadWasCreated;
use App\Factory\LeadFactory;
use App\Mail\TestMail;
use App\Models\Account;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\LeadRepository;
use App\Requests\SearchRequest;
use App\Search\LeadSearch;
use App\Transformations\TaskTransformable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadTest extends TestCase
{

    use DatabaseTransactions, WithFaker, TaskTransformable;

    private $user;
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();

        config(['mail.driver' => 'log']);
    }

    /** @test */
    public function it_can_show_all_the_leads()
    {
        Lead::factory()->create();
        $list = (new LeadSearch(new LeadRepository(new Lead)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
        // $this->assertInstanceOf(Collection::class, $list);
        //$this->assertEquals($insertedtask->name, $myLastElement['name']);
    }

    /** @test */
    public function it_can_delete_the_lead()
    {
        $lead = Lead::factory()->create();
        $deleted = $lead->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_lead()
    {
        $lead = Lead::factory()->create();
        $deleted = $lead->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_lead()
    {
        $lead = Lead::factory()->create();
        $data = ['first_name' => $this->faker->firstName];
        $leadRepo = new LeadRepository($lead);
        $lead = $leadRepo->save($data, $lead);
        $lead = $leadRepo->findLeadById($lead->id);
        $this->assertInstanceOf(Lead::class, $lead);

        $this->assertEquals($data['first_name'], $lead->first_name);
    }

    /** @test */
    public function it_can_show_the_lead()
    {
        $lead = Lead::factory()->create();
        $leadRepo = new LeadRepository(new Lead);
        $found = $leadRepo->findLeadById($lead->id);
        $this->assertInstanceOf(Lead::class, $found);
        $this->assertEquals($lead->first_name, $found->first_name);
    }


    /** @test */
    public function it_can_create_a_lead()
    {
        $data = [
            'account_id'     => $this->account->id,
            'user_id'        => $this->user->id,
            'task_status_id' => 1,
            'name'           => $this->faker->word,
            'description'    => $this->faker->sentence,
            'first_name'     => $this->faker->firstName,
            'last_name'      => $this->faker->lastName,
            'phone'          => $this->faker->phoneNumber,
            'email'          => $this->faker->safeEmail
        ];

        $leadRepo = new LeadRepository(new Lead);
        $factory = (new LeadFactory)->create($this->account, $this->user);
        $lead = $leadRepo->createLead($factory, $data);

        event(new LeadWasCreated($lead));

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertEquals($data['first_name'], $lead->first_name);
    }

    /** @test */
    public function it_can_convert_a_lead()
    {
        $lead = Lead::factory()->create();
        $lead = $lead->service()->convertLead();
        $this->assertInstanceOf(Lead::class, $lead);
    }

    /** @test */
     public function incoming_mail_is_saved_to_the_leads_table() {
        // Given: we have an e-mail﻿
        $email = new TestMail(
            $sender = 'sender@example.com',
            $subject = 'Test E-mail',
            $body = 'Some example text in the body'
        );

        $user = User::whereId(5)->first();
        Auth::login($user);

        // When: we receive that e-mail
        Mail::to('leads@tamtamcrm.com')->send($email);

        // Then: we assert the e-mails (meta)data was stored
        $lead = Lead::whereName($subject)->first();
        $this->assertInstanceOf(Lead::class, $lead);
       
//        tap($lead, function ($mail) use ($sender, $subject, $body) {
//
//            $this->assertEquals($sender, $mail->sender);
//            $this->assertEquals($subject, $mail->subject);
//            $this->assertContains($body, $mail->body);
//        });
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
