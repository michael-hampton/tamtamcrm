<?php
// https://codingofcents.com/laravel/how-to-send-email-with-attachment-with-laravel/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceWithAttachment extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $attachment;
    private $entity;

    /**
     * Create a new message instance.
     *
     * @param $content
     * @param $attachment
     * @param $entity
     */
    public function __construct($content, $attachment, $entity)
    {
        $this->content = $content;
        $this->attachment = $attachment;
        $this->entity = $entity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'email.admin.download',
            ['logo' => $this->entity->account->present()->logo, 'url' => $this->attachment]
        )
                    ->from(config('mail.from.address'))
                    ->subject(trans('texts.download_attachments'))
                    ->with('content', $this->content)
                    ->attach($this->attachment);
    }
}
