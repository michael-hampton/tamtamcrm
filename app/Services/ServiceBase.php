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
}
