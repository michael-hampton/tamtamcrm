<?php

namespace App\Mail\Admin;

use App\Models\Invitation;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerContactViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;
use ReflectionException;

class ObjectSent extends AdminMailer
{
    use Queueable, SerializesModels;

    private string $entity_name;
    private $contact;

    /**
     * ObjectSent constructor.
     * @param Invitation $invitation
     * @param User $user
     * @throws ReflectionException
     */
    public function __construct(Invitation $invitation, User $user)
    {
        $this->entity_name = strtolower((new ReflectionClass($invitation->inviteable))->getShortName());
        $this->invitation = $invitation;
        $this->contact = get_class(
            $invitation->inviteable
        ) === 'App\\Models\\PurchaseOrder' ? $invitation->company_contact : $invitation->contact;
        $this->entity = $invitation->inviteable;
        $this->user = $user;

        parent::__construct("{$this->entity_name}_sent", $invitation->inviteable, $invitation);
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
        $this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'    => $this->invitation->inviteable->getFormattedTotal(),
            'customer' => (new CustomerContactViewModel($this->contact))->name(),
            'invoice'  => $this->invitation->inviteable->getNumber(),
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => $this->getUrl() . "view/{$this->entity_name}/" . $this->invitation->key .
                "?silent=true",
            'button_text' => trans("texts.view_{$this->entity_name}"),
        ];
    }
}
