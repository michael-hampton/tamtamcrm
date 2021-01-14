<?php

namespace App\Mail;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Laracasts\Presenter\Exceptions\PresenterException;

class DownloadInvoices extends Mailable
{
    use Queueable, SerializesModels;

    public $file_path;

    public $account;

    public function __construct($file_path, Account $account)
    {
        $this->file_path = $file_path;

        $this->account = $account;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws PresenterException
     */
    public function build()
    {
        return $this->subject(trans('texts.download'))->markdown(
            'email.admin.download_files',
            [
                'url'  => $this->file_path,
                'logo' => $this->account->present()->logo,
            ]
        );
    }
}
