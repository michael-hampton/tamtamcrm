<?php

namespace App\Components\Reports;

use App\Components\Pdf\StatementPdf;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;

class PaymentReport extends BaseReport
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
        'completed' => [
            'header' => '',
            'body'   => ''
        ],
        'pending'   => [
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
        $this->entities = $customer->payments;
    }

    public function buildStatement()
    {
        $table_data = $this->getTableData();
        return $this->buildTable($table_data, $this->table_structure);
    }

    private function getTableData()
    {
        $tables = [];

        foreach ($this->entities as $key => $payment) {
            if (!in_array($payment->status_id, [Payment::STATUS_COMPLETED, Payment::STATUS_PENDING])) {
                continue;
            }

            $item = [
                '$total'    => $payment->amount,
                '$balance'  => $payment->applied,
                '$due_date' => '',
                '$date'     => $this->objPdf->formatDate($this->customer, $payment->date),
                '$customer' => $payment->customer->name,
                '$status'   => ($payment->status_id === Invoice::STATUS_PAID) ? '<div class="badge badge-success">' . trans(
                        'texts.status_paid'
                    ) . '</div>' : '<div class="badge badge-primary">' . trans('texts.status_pending') . '</div>'

            ];

            $label = $payment->status_id === Payment::STATUS_COMPLETED ? 'completed' : 'pending';

            $tables[$label][] = $item;

            if (!isset($totals[$label])) {
                $this->totals[$label] = [
                    'balance_due' => 0,
                    'total'       => 0
                ];
            }

            $this->totals[$label]['balance_due'] += $payment->applied;
            $this->totals[$label]['total'] += $payment->amount;
        }

        return $tables;
    }

    public function getTotals()
    {
        return $this->totals;
    }
}