<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @var string
     */
    private $view_link = '';
    private $view_text = '';

    private $entity;
    private $data;
    /**
     * @var string
     */
    private $design = '';

    /**
     * @var string
     */
    private $template = '';
    private $contact;

    /**
     * Create a new message instance.
     *
     * @param $entity
     * @param $contact
     */
    public function __construct($entity, $contact)
    {
        $this->entity = $entity;
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $design = "email.template.{$this->design}";

        return $this->from(
            $this->entity->user->email,
            $this->entity->user->present()->name()
        )
                    ->text(
                        $design,
                        [
                            'body'      => $this->body,
                            'view_link' => $this->view_link,
                            'view_text' => $this->view_text
                        ]
                    )
                    ->view($design, $this->data);
    }

    /**
     * @param mixed $subject
     * @return SendMail
     * @return SendMail
     */
    public function setSubject($subject): self
    {
        $this->subject($subject);
        return $this;
    }

    /**
     * @param mixed $body
     * @return SendMail
     * @return SendMail
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param mixed $footer
     * @return SendMail
     * @return SendMail
     */
    public function setFooter($footer): self
    {
        if (!empty($footer)) {
            $this->view_link = $footer['link'];
            $this->view_text = $footer['text'];
        }

        $this->footer = $footer;
        return $this;
    }

    /**
     * @param mixed $data
     * @return SendMail
     * @return SendMail
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @param $cc_address
     * @return SendMail
     */
    public function setCc($cc_address): self
    {
        $this->cc($cc_address);
        return $this;
    }

    /**
     * @param $bcc_address
     * @return SendMail
     */
    public function setBcc($bcc_address): self
    {
        $this->bcc($bcc_address);
        return $this;
    }

    /**
     * @param $design
     * @return SendMail
     */
    public function setDesign($design): self
    {
        $this->design = $design;
        return $this;
    }

    public function setTemplate($template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param $reply_to_address
     * @return SendMail
     */
    public function setReplyTo($reply_to_address): self
    {
        $this->replyTo($reply_to_address);
        return $this;
    }

    /**
     * @param mixed $attachment
     * @return SendMail
     * @return SendMail
     */
    public function setAttachments($attachment): self
    {
        if (empty($attachment)) {
            return $this;
        }

        $this->attach($attachment);
        return $this;
    }

    /**
     * @param mixed $attachmentData
     * @param string $filename
     * @return SendMail
     */
    public function setAttachmentData(string $attachmentData, string $filename): self
    {
        $this->attachData($attachmentData, $filename);
        return $this;
    }
}
