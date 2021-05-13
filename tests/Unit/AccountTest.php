<?php

namespace Tests\Unit;

use App\Actions\Account\ConvertAccount;
use App\Actions\Account\CreateAccount;
use App\Jobs\CreateAccountDataExportJob;
use App\Jobs\ProcessSubscription;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\DomainRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_can_convert_the_account()
    {
        $account = Account::factory()->create();
        $account = (new ConvertAccount($account))->execute();

        $this->assertInstanceOf(Account::class, $account);
        $this->assertInstanceOf(Customer::class, $account->domains->customer);
        $this->assertInstanceOf(User::class, $account->domains->user);
        $this->assertEquals(1, $account->domains->customer->contacts->count());
    }

    /** @test */
    public function it_can_create_an_account()
    {
        $user = (new CreateAccount())->execute(
            ['email' => $this->faker->safeEmail, 'password' => $this->faker->password]
        );

        $domain = $user->domain;

        $this->assertNotNull($domain->plan_id);

        $plan = $domain->plans->first();

        $this->assertEquals($plan->starts_at->format('Y-m-d'), now()->format('Y-m-d'));
        $this->assertEquals($plan->ends_at->format('Y-m-d'), now()->addYearNoOverflow()->format('Y-m-d'));
        $this->assertEquals($plan->due_date->format('Y-m-d'), now()->addMonthNoOverflow()->format('Y-m-d'));
        $this->assertEquals($plan->plan->code, 'STDM');
    }

    public function test_export_account_data()
    {
        $account = Account::where('id', 1)->first();
        $user = User::find(5);

        dispatch(new CreateAccountDataExportJob($account, $user));

        $allFiles = Storage::disk('public')->allFiles();
        $this->assertCount(1, $allFiles);

        $zipPath = $this->getFullPath($this->diskName, $allFiles[0]);
        $this->assertZipContains($zipPath, 'attributes.json', json_encode($user->attributesToArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->assertZipContains($zipPath, 'avatar.png');
        $this->assertZipContains($zipPath, 'thumbnail.png');

        Notification::assertSentTo($user, PersonalDataExportedNotification::class, function (PersonalDataExportedNotification $notification) use ($allFiles, $user) {
            if ($notification->zipFilename !== $allFiles[0]) {
                return false;
            }

            return true;
        });

        Event::assertDispatched(PersonalDataSelected::class);
        Event::assertDispatched(PersonalDataExportCreated::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
