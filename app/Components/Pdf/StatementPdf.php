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

    public function getTable($design)
    {
        $columns = $this->entity->account->settings->pdf_variables->statement_columns;

        $table_data = $this->buildTable($columns);

        $statement_html = $design->statement_table;

        $table_html = '';

        foreach ($table_data as $key => $table_datum) {
            $table_html .= '<h3 class="mt-3">' . trans('texts.' . $key) . '</h3>';

            foreach ($table_datum as $table_type => $item) {
                if (empty($item['header'])) {
                    continue;
                }

                $table_html .= '<h3 class="mt-3">' . trans('texts.' . $table_type) . '</h3>';
                $table_html .= str_replace(
                    ['$statement_table_header', '$statement_table_body'],
                    [$item['header'], $item['body']],
                    $statement_html
                );
            }
        }


        return $table_html;
    }

    public function getTotals()
    {
        return $this->totals;
    }
}