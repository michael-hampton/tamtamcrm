<?php


namespace App\Components\Pdf;


use App\Models\Invoice;
use ReflectionClass;
use ReflectionException;
use stdClass;

class PurchaseOrderPdf extends PdfBuilder
{
    protected $entity;

    private $entity_string = 'purchase_order';

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

    public function getEntityString()
    {
        return $this->entity_string;
    }

    public function build($contact = null)
    {
        $contact = $contact === null ? $this->entity->company->contacts->first() : $contact;
        $company = $this->entity->company;

        $this->buildContact($contact)
             ->setTaxes($company)
             ->setDate($this->entity->date)
             ->setDueDate($this->entity->due_date)
             ->setNumber($this->entity->number)
             ->setPoNumber($this->entity->po_number)
             ->buildCompany($company)
             ->buildCompanyAddress($company)
             ->buildAccount($this->entity->account)
             ->setTerms($this->entity->terms)
             ->setFooter($this->entity->footer)
             ->setDiscount($company, $this->entity->discount_total)
             ->setShippingCost($company, $this->entity->shipping_cost)
             ->setVoucherCode(isset($this->entity->voucher_code) ? $this->entity->voucher_code : '')
             ->setSubTotal($company, $this->entity->sub_total)
             ->setBalance($company, $this->entity->balance)
             ->setTotal($company, $this->entity->total)
             ->setNotes($this->entity->customer_note)
            //->setInvoiceCustomValues()
             ->buildProduct()
             ->transformLineItems($company, $this->entity)
             ->buildTask();

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

    public function getTable($design)
    {
        $table_data = $this->buildTable($this->getTableColumns());

        $invoice_html = $design->table;

        $table_html = '';

        $translations = [
            Invoice::TASK_TYPE    => 'tasks',
            Invoice::EXPENSE_TYPE => 'expenses',
            Invoice::PRODUCT_TYPE => 'products'
        ];

        foreach ($table_data as $key => $item) {
            if (empty($item['header'])) {
                continue;
            }

            $table_html .= '<h3 class="mt-3">' . trans('texts.' . $translations[$key]) . '</h3>';


            $table_html .= str_replace(
                ['$product_table_header', '$product_table_body'],
                [$item['header'], $item['body']],
                $invoice_html
            );
        }

        return $table_html;
    }

    /**
     * @param $columns
     * @return array|stdClass
     */
    public function buildTable($columns)
    {
        $header = [];
        $table_row = [];

        $labels = $this->getLabels();
        $values = $this->getValues();

        if (empty($this->line_items)) {
            return [];
        }

        foreach ($columns as $key => $column) {
            $header[$column] = '<td>' . $labels[$column . '_label'] . '</td>';
            $table_row[$column] = '<td class="table_header_td_class">' . $column . '</td>';
        }

        $table_structure = [
            Invoice::PRODUCT_TYPE => [
                'header' => '',
                'body'   => ''
            ],
            Invoice::TASK_TYPE    => [
                'header' => '',
                'body'   => ''
            ],
            Invoice::EXPENSE_TYPE => [
                'header' => '',
                'body'   => ''
            ]
        ];

        $types = array_keys($table_structure);

        foreach ($types as $type) {
            if (!empty($this->line_items[$type])) {
                $empty_columns = $this->checkIfEmpty($this->line_items, $type);

                /********* Remove empty columns ***********************/
                if (!empty($empty_columns) && $this->entity->account->settings->dont_display_empty_pdf_columns === true) {
                    foreach (array_values($empty_columns) as $empty_column) {
                        unset($header[$empty_column]);
                        unset($table_row[$empty_column]);
                    }
                }

                $table_structure[$type]['header'] .= '<tr>' . strtr(implode('', $header), $labels) . '</tr>';

                foreach ($this->line_items[$type] as $data) {
                    $tmp = strtr(implode('', $table_row), $data);
                    $tmp = strtr($tmp, $values);

                    $table_structure[$type]['body'] .= '<tr>' . $tmp . '</tr>';
                }
            }
        }

        return $table_structure;
    }

    private function getTableColumns()
    {
        $input_variables = json_decode(json_encode($this->entity->account->settings->pdf_variables), true);
        return $input_variables['product_columns'];
    }
}
