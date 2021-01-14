<?php


namespace App\Components\Pdf;


use App\Models\Lead;
use Laracasts\Presenter\Exceptions\PresenterException;
use ReflectionClass;
use ReflectionException;

class LeadPdf extends PdfBuilder
{
    protected $entity;

    /**
     * InvoicePdf constructor.
     * @param $entity
     * @throws ReflectionException
     */
    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->entity = $entity;
        $this->class = strtolower((new ReflectionClass($this->entity))->getShortName());
    }

    public function build($contact = null)
    {
        $this->buildClientForLead($this->entity)
             ->buildAddress($this->entity, $this->entity)
             ->buildAccount($this->entity->account);

        foreach ($this->data as $key => $value) {
            if (isset($value['label'])) {
                $this->labels[$key . '_label'] = $value['label'];
            }

            if (isset($value['value'])) {
                $this->values[$key] = $value['value'];
            }
        }

        return $this;
    }

    /**
     * @param Lead $lead
     * @return $this
     * @throws PresenterException
     */
    private function buildClientForLead(Lead $lead): self
    {
        $this->data['$customer.website'] = [
            'value' => $lead->present()->website() ?: '&nbsp;',
            'label' => trans('texts.website')
        ];
        $this->data['$customer.phone'] = [
            'value' => $lead->present()->phone() ?: '&nbsp;',
            'label' => trans('texts.phone_number')
        ];
        $this->data['$customer.email'] = ['value' => $lead->email, 'label' => trans('texts.email_address')];
        $this->data['$customer.name'] = [
            'value' => $lead->present()->name() ?: '&nbsp;',
            'label' => trans('texts.customer_name')
        ];
        $this->data['$customer1'] = [
            'value' => $lead->custom_value1 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value1')
        ];
        $this->data['$customer2'] = [
            'value' => $lead->custom_value2 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value2')
        ];
        $this->data['$customer3'] = [
            'value' => $lead->custom_value3 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value3')
        ];
        $this->data['$customer4'] = [
            'value' => $lead->custom_value4 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value4')
        ];

        return $this;
    }

    public function buildTable()
    {
        return '';
    }
}