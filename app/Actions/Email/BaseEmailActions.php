<?php

namespace App\Actions\Email;


use App\Jobs\Email\SendEmail;
use App\Models\ContactInterface;

class BaseEmailActions
{

    protected $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $template
     * @param null $contact
     * @return bool
     */
    protected function sendInvitationEmails(string $subject, string $body, string $template, $contact = null)
    {

        if ($this->entity->invitations->count() === 0) {
            return false;
        }

        if ($contact !== null) {
            $invitation = $this->entity->invitations->where('contact_id', '=', $contact->id)->first();

            $section = $invitation->getSection();

            $footer = ['link' => $invitation->getLink(), 'text' => trans('texts.view_' . $section)];
            return $this->dispatchEmail($contact, $subject, $body, $template, $footer, $invitation);
        }

        foreach ($this->entity->invitations as $invitation) {
            $contact = get_class(
                $invitation->inviteable
            ) === 'App\\Models\\PurchaseOrder' ? $invitation->company_contact : $invitation->contact;

            $section = $invitation->getSection();

            $footer = ['link' => $invitation->getLink(), 'text' => trans('texts.view_' . $section)];

            $this->dispatchEmail($contact, $subject, $body, $template, $footer, $invitation);
        }

        return true;
    }

    /**
     * @param ContactInterface $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @param array $footer
     * @param null $invitation
     * @return bool
     * @throws \ReflectionException
     */
    protected function dispatchEmail(
        ContactInterface $contact,
        string $subject,
        string $body,
        string $template,
        array $footer,
        $invitation = null
    ) {
        if (!$contact->email_notification_enabled || !$contact->email) {
            return false;
        }

        SendEmail::dispatchNow($this->entity, $subject, $body, $template, $contact, $footer);

        $this->triggerEvent($invitation, $template);

        return true;
    }

    protected function triggerEvent($model = null, $template = '')
    {
        $entity_class = (new \ReflectionClass($this->entity))->getShortName();
        $event_class = "App\Events\\" . $entity_class . "\\" . $entity_class . "WasEmailed";

        if (class_exists($event_class)) {

            event(new $event_class($model === null ? $this->entity : $model, $template));
        }

        return true;
    }
}