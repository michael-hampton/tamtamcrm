<?php

namespace App\Mail\Admin;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ObjectViewed extends AdminMailer
{
    use Queueable, SerializesModels;

    private $invitation;
    private string $entity_name;
    private $contact;

    /**
     * ObjectViewed constructor.
     * @param Invitation $invitation
     * @param $entity_name
     * @param User $user
     */
    public function __construct(Invitation $invitation, $entity_name, User $user)
    {
        $this->entity_name = $entity_name;
        $this->entity = $invitation->inviteable;
        $this->contact = $invitation->contact;
        $this->invitation = $invitation;
        $this->user = $user;

        parent::__construct("{$this->entity_name}_viewed", $this->entity);
    }

    /**
     * Build the message.
     *
     * @return void
     */
    public function build()
    {
        $data = $this->getData();

        $this->setSubject($data);
        $this->setMessage($data);
        $this->execute($this->buildMessage());
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'            => $this->entity->getFormattedTotal(),
            'customer'         => $this->contact->present()->name(),
            $this->entity_name => $this->entity->getNumber()
        ];
    }

    /**
     * @return array
     */
    public function buildMessage(): array
    {
        return [
            'title'       => $this->subject,
            'body'        => $this->message,
            'url'         => config(
                    'taskmanager.site_url'
                ) . "/portal/view/{$this->entity_name}/" . $this->invitation->key .
                "?silent=true",
            'button_text' => trans("texts.view_{$this->entity_name}"),
            'signature'   => isset($this->entity->account->settings->email_signature) ? $this->entity->account->settings->email_signature : '',
            'logo'        => $this->entity->account->present()->logo(),
        ];
    }
}
