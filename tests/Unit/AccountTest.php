<?php

namespace Tests\Unit;

use App\Services\Account\ConvertAccount;
use App\Services\Account\CreateAccount;
use App\Components\Import\ImportAccountData;
use App\Events\Account\AccountDataExportCreated;
use App\Events\Account\AccountDataSelected;
use App\Jobs\CreateAccountDataExportJob;
use App\Jobs\ProcessSubscription;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\Account\AccountDataExportedNotification;
use App\Repositories\DomainRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Assert;
use Spatie\TemporaryDirectory\TemporaryDirectory;
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

        $directories = glob(public_path(config('taskmanager.downloads_dir')) . '/*' , GLOB_ONLYDIR);
        $previous_count = count($directories);

        dispatch(new CreateAccountDataExportJob($account, $user));

        $directories = glob(public_path(config('taskmanager.downloads_dir')) . '/*' , GLOB_ONLYDIR);
        $current_count = count($directories);

        $this->assertEquals($current_count, $previous_count + 1);
        $zipPath = end($directories);

        $files = array_values(array_diff(scandir($zipPath), array('.', '..')));

        $this->assertZipContains($zipPath . '/' . $files[0], 'attributes.json', json_encode($account->selectPersonalData(null, true), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        //(new ImportAccountData($zipPath . '/' . $files[0], 'attributes.json'))->importData();

        die('here');

    }

    public function getFullPath(string $diskName, string $filename): string
    {
        return Storage::disk($diskName)->getDriver()->getAdapter()->getPathPrefix().'/'.$filename;
    }

    public function assertZipContains($zipFile, $expectedFileName, $expectedContents = null)
    {
        Assert::assertFileExists($zipFile);

        $zip = new \ZipArchive();

        $zip->open($zipFile);

        $temporaryDirectory = (new TemporaryDirectory())->create();

        $zipDirectoryName = 'extracted-files';

        $zip->extractTo($temporaryDirectory->path($zipDirectoryName));

        $expectedZipFilePath = $temporaryDirectory->path($zipDirectoryName.'/'.$expectedFileName);

        Assert::assertFileExists($expectedZipFilePath);

        if (is_null($expectedContents)) {
            return;
        }

        $actualContents = file_get_contents($expectedZipFilePath);

        Assert::assertEquals(json_decode($expectedContents, true), json_decode($actualContents, true));
    }


    public function tearDown(): void
    {
        parent::tearDown();
    }

}
