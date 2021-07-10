<?php


namespace App\Jobs;


use App\Components\Export\AccountDataSelection;
use App\Components\Export\Zip;
use App\Events\Account\AccountDataExportCreated;
use App\Events\Account\AccountDataSelected;
use App\Models\Account;
use App\Models\User;
use App\Notifications\Account\AccountDataExportedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class CreateAccountDataExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Account $account;

    private User $user;

    public function __construct(Account $account,User $user)
    {
        $this->account = $account;
        $this->user = $user;
    }

    public function handle()
    {
        $temporaryDirectory = (new TemporaryDirectory(public_path(config('taskmanager.downloads_dir'))))->create();

        $accountDataSelection = $this->selectAccountData($temporaryDirectory);

        event(new AccountDataSelected($accountDataSelection, $this->account));

        $zipFilename = $this->zipPersonalData($accountDataSelection, $this->getDisk(), $temporaryDirectory);

        //$temporaryDirectory->delete();

        event(new AccountDataExportCreated($zipFilename, $this->account));

        $this->notifyZip($temporaryDirectory->path($zipFilename));
    }

    protected function selectAccountData(TemporaryDirectory $temporaryDirectory): AccountDataSelection
    {
        $personalData = (new AccountDataSelection($temporaryDirectory))->forAccount($this->account);

        $this->account->selectPersonalData($personalData);

        return $personalData;
    }

    public function getDisk(): Filesystem
    {
        return Storage::disk(config('taskmanager.disk'));
    }

    protected function zipPersonalData(
        AccountDataSelection $accountData,
        Filesystem $filesystem,
        TemporaryDirectory $temporaryDirectory
    ): string {
        $zip = Zip::createForAccountData($accountData, $temporaryDirectory);

        $zipFilename = pathinfo($zip->path(), PATHINFO_BASENAME);

        $filesystem->writeStream($zipFilename, fopen($zip->path(), 'r'));

        return $zipFilename;
    }

    protected function notifyZip(string $zipFilename)
    {

        $this->user->notify(new AccountDataExportedNotification($zipFilename));
    }
}