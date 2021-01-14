<?php

namespace App\Components\Reports;

use App\Components\Pdf\StatementPdf;
use App\Models\Customer;
use App\Models\Invoice;

class InvoiceReport extends BaseReport
{

    protected array $translations = [
        '$total'    => 'texts.total',
        '$balance'  => 'texts.balance_due',
        '$due_date' => 'texts.date',
        '$date'     => 'texts.due_date',
        '$status'   => 'texts.status',
        '$customer' => 'texts.customer',
        '$contact'  => 'texts.contact_name'
    ];
    protected array $table_structure = [
        'outstanding' => [
            'header' => '',
            'body'   => ''
        ],
        'paid'        => [
            'header' => '',
            'body'   => ''
        ]
    ];
    private array $totals = [];
    /**
     * @var Customer
     */
    private Customer $customer;

    private $entities;

    public function __construct(Customer $customer, StatementPdf $objPdf, array $columns)
    {
        parent::__construct($columns, $this->translations, $objPdf);
        $this->customer = $customer;
        $this->entities = $customer->invoices;
    }

    public function buildStatement()
    {
        $table_data = $this->getTableData();
        return $this->buildTable($table_data, $this->table_structure);
    }

    private function getTableData()
    {
        $tables = [];
        $totals = [];
        $balance_due = 0;
        $total = 0;

        foreach ($this->entities as $key => $invoice) {
            if (!in_array($invoice->status_id, [Invoice::STATUS_SENT, Invoice::STATUS_PAID, Invoice::STATUS_PARTIAL])) {
                continue;
            }

            $item = [
                '$total'    => $invoice->total,
                '$balance'  => $invoice->balance,
                '$due_date' => $this->objPdf->formatDate($this->customer, $invoice->due_date),
                '$date'     => $this->objPdf->formatDate($this->customer, $invoice->date),
                '$customer' => $invoice->customer->name,
                '$status'   => ($invoice->status_id === Invoice::STATUS_SENT)
                    ? '<div class="badge badge-primary">' . trans(
                        'texts.status_sent'
                    ) . '</div>'
                    : (($invoice->status_id === Invoice::STATUS_PAID) ? '<div class="badge badge-success">' . trans(
                            'texts.status_paid'
                        ) . '</div>' : '<div class="badge badge-primary">' . trans('texts.status_partial') . '</div>')
            ];

            $label = in_array(
                $invoice->status_id,
                [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL]
            ) ? 'outstanding' : 'paid';

            $tables[$label][] = $item;

            if (!isset($totals[$label])) {
                $this->totals[$label] = [
                    'balance_due' => 0,
                    'total'       => 0
                ];
            }

            $this->totals[$label]['balance_due'] += $invoice->balance;
            $this->totals[$label]['total'] += $invoice->total;
        }

        return $tables;
    }

    public function getTotals()
    {
        return $this->totals;
    }
}