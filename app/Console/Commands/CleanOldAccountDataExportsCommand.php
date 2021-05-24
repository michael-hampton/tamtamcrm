<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CleanOldAccountDataExportsCommand extends Command
{
    protected $signature = 'account-data-export:clean';

    protected $description = 'Remove old account downloads';

    public function handle()
    {
        $this->comment('Start deleting old account downloads...');

        $oldZipFiles = collect($this->getDisk()->allFiles())
            ->filter(fn (string $zipFilename) => Str::endsWith($zipFilename, '.zip'))
            ->filter(function (string $zipFilename) {
                $zipFilenameParts = explode('_', $zipFilename);

                if (! isset($zipFilenameParts[1])) {
                    return false;
                }

                $dateCreated = Carbon::createFromTimestamp($zipFilenameParts[1]);

                $threshold = now()->subDays(config('taskmanager.delete_account_data_after_days'));

                return $dateCreated->isBefore($threshold);
            })
            ->toArray();

        $this->getDisk()->delete($oldZipFiles);

        $this->comment(count($oldZipFiles).' old zip files have been deleted.');

        $this->info('All done!');
    }

    protected function getDisk(): Filesystem
    {
        return Storage::disk('public');
    }
}
