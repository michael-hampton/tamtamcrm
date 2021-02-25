<?php

namespace App\Actions\Email;


use App\Jobs\Email\SendEmail;
use Exception;
use ReflectionClass;
use ReflectionException;

class DispatchEmail extends BaseEmailActions
{
    public function __construct($entity)
    {
//        echo '<pre>';
//        print_r($entity);

        parent::__construct($entity);
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return void|null
     * @throws ReflectionException
     */
    public function execute($contact = null, $subject = '', $body = '', $template = 'invoice')
    {
        $entity_string = (new ReflectionClass($this->entity))->getShortName();

        if (in_array($entity_string, ['Lead', 'Task', 'Deal'])) {
            return $this->dispatch($subject, $body, $contact, strtolower($entity_string));
        }

        if ($entity_string === 'Payment') {
            return $this->sendPaymentEmails();
        }

        if (!$this->sendInvitationEmails($subject, $body, $template, $contact)) {
            return null;
        }

        return $this->entity;
    }

    private function dispatch($subject, $body, $contact, $entity_string)
    {
        if (empty($contact) && $entity_string === 'Task') {
            $contact = $this->entity->customer->primary_contact()->first();
        }

        if (empty($subject) || empty($body)) {
            throw new Exception('Subject was empty');
        }

        SendEmail::dispatchNow($this->entity, $subject, $body, $entity_string, $contact);

        $this->triggerEvent();
    }

    public function sendPaymentEmails()
    {
        $subject = $this->entity->customer->getSetting('email_subject_payment');
        $body = $this->entity->customer->getSetting('email_template_payment');

        foreach ($this->entity->customer->contacts as $contact) {
            $footer = ['link' => $this->entity->getUrl(), 'text' => trans('texts.view_payment')];
            $this->dispatchEmail($contact, $subject, $body, 'email_template_payment', $footer);
        }

        $this->triggerEvent();
    }
}