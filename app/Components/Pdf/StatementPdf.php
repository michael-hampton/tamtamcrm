<?php


namespace App\Components\Pdf;


use App\Components\Reports\InvoiceReport;
use App\Components\Reports\PaymentReport;
use ReflectionClass;
use ReflectionException;

class StatementPdf extends PdfBuilder
{
    protected $entity;

    private array $totals = [];

    /**
     * TaskPdf constructor.
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
        $contact = $contact === null ? $this->entity->contacts->first() : $contact;
        $customer = $this->entity;

        $this->setDefaults($customer)
             ->buildContact($contact)
             ->buildCustomer($customer)
             ->buildCustomerAddress($customer)
             ->buildAccount($this->entity->account)
             ->setTerms($this->entity->terms)
             ->setFooter($this->entity->footer)
             ->setNotes($this->entity->public_notes)
             ->buildStatement();

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

    public function buildStatementTables()
    {
        $columns = $this->entity->account->settings->pdf_variables->statement_columns;

        return $this->buildTable($columns);
    }

    private function buildTable($columns)
    {
        $tables = [];

        $objInvoiceReport = (new InvoiceReport($this->entity, $this, $columns));
        $objPaymentReport = (new PaymentReport($this->entity, $this, $columns));

        $tables['invoice'] = $objInvoiceReport->buildStatement();

        $this->totals['invoice'] = $objInvoiceReport->getTotals();
        $this->totals['payment'] = $objPaymentReport->getTotals();

        $tables['payment'] = $objPaymentReport->buildStatement();

        return $tables;
    }

    public function getTotals()
    {
        return $this->totals;
    }
}