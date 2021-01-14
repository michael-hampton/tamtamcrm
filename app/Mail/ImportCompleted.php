<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportCompleted extends Mailable
{
    use Queueable, SerializesModels;

    private array $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('taskmanager.from_email'), config('taskmanager.from_name'))
                    ->view('email.admin.import_completed')->with($this->data);
    }
}
