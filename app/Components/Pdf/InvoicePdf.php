<?php


namespace App\Components\Pdf;


use App\Models\Invoice;
use ReflectionException;
use stdClass;

class InvoicePdf extends PdfBuilder
{
    protected $entity;

    /**
     * @var string
     */
    private string $entity_string = '';

    /**
     * InvoicePdf constructor.
     * @param $entity
     * @param string $entity_string
     * @throws ReflectionException
     */
    public function __construct($entity, $entity_string = '')
    {
        parent::__construct($entity, $entity_string);
        $this->entity = $entity;
        $this->entity_string = $entity_string;
    }

    public function getEntityString()
    {
        return $this->entity_string;
    }

    public function build($contact = null)
    {
        $contact = $contact === null ? $this->entity->customer->contacts->first() : $contact;
        $customer = $this->entity->customer;

        $this->setDefaults($customer)
             ->setStatus()
             ->buildContact($contact)
             ->setTaxes($customer)
             ->setDatetime($this->entity->created_at)
             ->setDate($this->entity->date)
             ->setDueDate($this->entity->due_date)
             ->setNumber($this->entity->number)
             ->setPoNumber($this->entity->po_number)
             ->buildCustomer($customer)
             ->buildCustomerAddress($customer)
             ->buildAccount($this->entity->account)
             ->setTerms($this->entity->terms)
             ->setFooter($this->entity->footer)
             ->setUser($this->entity->user)
             ->setDiscount($customer, $this->entity->discount_total)
             ->setShippingCost($customer, $this->entity->shipping_cost)
             ->setVoucherCode(isset($this->entity->voucher_code) ? $this->entity->voucher_code : '')
             ->setSubTotal($customer, $this->entity->sub_total)
             ->setBalance($customer, $this->entity->balance)
             ->setTotal($customer, $this->entity->total)
//             ->setCustomerBalance($customer)
//             ->setCustomerPaidToDate($customer)
             ->setNotes($this->entity->public_notes)
             ->setInvoiceCustomValues()
             ->buildProduct()
             ->transformLineItems($customer, $this->entity)
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

    public function getTable($design, $entity_string, $entity)
    {
        $table_data = $this->buildTable($this->getTableColumns($entity_string));

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
    private function buildTable($columns)
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
                if (!empty($empty_columns) && $this->entity->customer->getSetting(
                        'dont_display_empty_pdf_columns'
                    ) === true) {
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

    private function getTableColumns($entity_string)
    {
        $input_variables = json_decode(json_encode($this->entity->account->settings->pdf_variables), true);
        return $entity_string === 'dispatch_note' ? $input_variables['dispatch_note_columns'] : $input_variables['product_columns'];
    }
}
