<?php


namespace App\Components\Export;


use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Zip
{
    protected \ZipArchive $zipFile;

    protected int $fileCount = 0;

    protected string $pathToZip;

    public function __construct(string $pathToZip)
    {
        $this->zipFile = new \ZipArchive();

        $this->pathToZip = $pathToZip;

        $this->open();
    }

    public static function createForAccountData(AccountDataSelection $accountDataSelection, TemporaryDirectory $temporaryDirectory): self
    {

        $zipFilenameParts = [
            $accountDataSelection->account->settings->name . '-' .$accountDataSelection->account->slug,
            now()->timestamp,
            Str::random(64),
        ];

        $zipFilename = implode('_', $zipFilenameParts) . '.zip';

        $pathToZip = $temporaryDirectory->path($zipFilename);

        return (new static($pathToZip))
            ->add($accountDataSelection->files(), $temporaryDirectory->path())
            ->close();
    }

    public function path(): string
    {
        return $this->pathToZip;
    }

    public function size(): int
    {
        if ($this->fileCount === 0) {
            return 0;
        }

        return filesize($this->pathToZip);
    }

    public function open(): self
    {
        $this->zipFile->open($this->pathToZip, \ZipArchive::CREATE);

        return $this;
    }

    public function add(array $files, string $rootPath): self
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $nameInZip = Str::after($file, $rootPath.'/');

                $this->zipFile->addFromString(basename($file), file_get_contents(ltrim($nameInZip, DIRECTORY_SEPARATOR)));

                unlink($file);
            }
            $this->fileCount++;
        }

        return $this;
    }

    public function close(): self
    {
        $this->zipFile->close();

        return $this;
    }
}