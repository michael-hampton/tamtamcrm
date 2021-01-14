<?php

namespace App\Services;

use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Components\Pdf\InvoicePdf;
use App\Factory\CloneOrderToInvoiceFactory;
use App\Jobs\Email\SendEmail;
use App\Jobs\Pdf\CreatePdf;
use App\Models\ContactInterface;
use ReflectionClass;
use ReflectionException;

class ServiceBase
{
    protected array $config = [];
    private $entity;

    public function __construct($entity, array $config = [])
    {
        $this->entity = $entity;
        $this->config = $config;
    }

    /**
     * @param null $contact
     * @param bool $update
     * @return mixed|string
     * @throws ReflectionException
     */
    public function generateDispatchNote($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->entity->customer->primary_contact()->first();
        }

        $entity = get_class($this->entity) === 'App\\Models\\Order' ? CloneOrderToInvoiceFactory::create(
            $this->entity,
            $this->entity->user,
            $this->entity->account
        ) : $this->entity;

        return CreatePdf::dispatchNow(
            (new InvoicePdf($entity)),
            $this->entity,
            $contact,
            $update,
            'dispatch_note'
        );
    }

    protected function trigger(string $subject, string $body, $repo)
    {
        if (empty($this->config)) {
            return false;
        }

        if (!empty($this->config['email'])) {
            $this->entity->service()->sendEmail(null, $subject, $body);
        }

        if (!empty($this->config['archive'])) {
            $this->entity->archive();
        }

        return true;
    }

    protected function reverseStatus()
    {
        $this->entity->setStatus($this->entity->previous_status);
        $this->entity->previous_status = null;

        $this->entity->save();
        return $this->entity;
    }

    protected function reverseBalance()
    {
        if (!isset($this->entity->previous_balance) || empty($this->entity->previous_balance)) {
            return $this->entity;
        }

        $previous_balance = $this->entity->previous_balance;
        $customer = $this->entity->customer->fresh();

        $customer->increaseBalance($previous_balance);

        $customer->save();

        $this->entity->transaction_service()->createTransaction(
            $previous_balance,
            $customer->balance,
            "Reverse Balance"
        );

        $this->entity->setBalance($previous_balance);
        $this->entity->previous_balance = null;

        $this->entity->save();
        return $this->entity;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $template
     * @param null $contact
     * @return bool
     * @throws ReflectionException
     */
    protected function sendInvitationEmails(string $subject, string $body, string $template, $contact = null)
    {
        if ($contact !== null) {
            $invitation = $this->entity->invitations->first();

            $section = $invitation->getSection();

            $footer = ['link' => $invitation->getLink(), 'text' => trans('texts.view_' . $section)];
            return $this->dispatchEmail($contact, $subject, $body, $template, $footer, $invitation);
        }

        if ($this->entity->invitations->count() === 0) {
            return false;
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
     * @throws ReflectionException
     */
    private function dispatchEmail(
        ContactInterface $contact,
        string $subject,
        string $body,
        string $template,
        array $footer,
        $invitation = null
    ) {
        if ($contact->send_email && $contact->email) {
            SendEmail::dispatchNow($this->entity, $subject, $body, $template, $contact, $footer);
        }

        $entity_class = (new ReflectionClass($this->entity))->getShortName();
        $event_class = "App\Events\\" . $entity_class . "\\" . $entity_class . "WasEmailed";

        if (class_exists($event_class) && $invitation !== null) {
            event(new $event_class($invitation, $template));
        }

        return true;
    }

    protected function calculateTotals($entity)
    {
        if (empty($this->entity->line_items)) {
            return $this->entity;
        }

        $objInvoice = (new InvoiceCalculator($this->entity))->build();

        return $objInvoice->rebuildEntity();
    }
}
