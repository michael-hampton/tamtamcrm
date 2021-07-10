<?php


namespace App\Components\Export;


use App\Exceptions\CouldNotAddToAccountDataSelection;
use App\Models\Account;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class AccountDataSelection
{
    protected array $files = [];

    public Account $account;

    private $temporaryDirectory;

    /**
     * AccountDataSelection constructor.
     * @param TemporaryDirectory $temporaryDirectory
     */
    public function __construct(TemporaryDirectory $temporaryDirectory) {
        $this->temporaryDirectory = $temporaryDirectory;
    }

    /**
     * @param Account $account
     * @return $this
     */
    public function forAccount(Account $account)
    {
        $this->account = $account;

        return $this;
    }

    public function add(string $nameInDownload, array $content): AccountDataSelection
    {
        $content = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $path = $this->temporaryDirectory->path($nameInDownload);

        $this->ensureDoesNotOverwriteExistingFile($path);

        $this->files[] = $path;

        file_put_contents($path, $content);

        return $this;
    }

    protected function ensureDoesNotOverwriteExistingFile(string $path)
    {
        if (file_exists($path)) {
            throw CouldNotAddToAccountDataSelection::fileAlreadyAddedToAccountDataSelection($path);
        }
    }

    public function files(): array
    {
        return $this->files;
    }
}