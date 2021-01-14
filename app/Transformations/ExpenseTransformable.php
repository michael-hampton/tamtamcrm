<?php

namespace App\Transformations;

use App\Models\Expense;
use App\Models\File;

trait ExpenseTransformable
{
    /**
     * @param Expense $expense
     * @return array
     */
    protected function transformExpense(Expense $expense)
    {
        return [
            'id'                   => $expense->id,
            'number'               => $expense->number ?: '',
            'user_id'              => $expense->user_id,
            'assigned_to'          => $expense->assigned_to,
            'company_id'           => $expense->company_id,
            'project_id'           => (int)$expense->project_id,
            'invoice_id'           => $expense->invoice_id,
            'customer_id'          => $expense->customer_id,
            'bank_id'              => (string)$expense->bank_id ?: '',
            'invoice_currency_id'  => (int)$expense->invoice_currency_id ?: '',
            'currency_id'          => (int)$expense->currency_id ?: '',
            'expense_category_id'  => (int)$expense->expense_category_id ?: '',
            'payment_type_id'      => (int)$expense->payment_type_id ?: '',
            'recurring_expense_id' => (int)$expense->recurring_expense_id ?: '',
            'is_deleted'           => (bool)$expense->is_deleted,
            'create_invoice'       => (bool)$expense->create_invoice,
            'include_documents'    => (bool)$expense->include_documents,
            'amount'               => (float)$expense->amount ?: 0,
            'converted_amount'     => (float)$expense->converted_amount ?: 0,
            'exchange_rate'        => (float)$expense->exchange_rate ?: 0,
            'tax_rate_name'        => $expense->tax_rate_name ? $expense->tax_rate_name : '',
            'tax_rate'             => (float)$expense->tax_rate,
            'private_notes'        => (string)$expense->private_notes ?: '',
            'public_notes'         => (string)$expense->public_notes ?: '',
            'reference_number'     => (string)$expense->reference_number ?: '',
            'transaction_id'       => (string)$expense->transaction_id ?: '',
            'date'                 => $expense->date ?: '',
            'payment_date'         => $expense->payment_date ?: '',
            'custom_value1'        => $expense->custom_value1 ?: '',
            'custom_value2'        => $expense->custom_value2 ?: '',
            'custom_value3'        => $expense->custom_value3 ?: '',
            'custom_value4'        => $expense->custom_value4 ?: '',
            'deleted_at'           => $expense->deleted_at,
            'updated_at'           => $expense->updated_at,
            'archived_at'          => $expense->deleted_at,
            'created_at'           => $expense->created_at,
            'files'                => $this->transformExpenseFiles($expense->files),
            'is_recurring'         => (bool)$expense->is_recurring ?: false,
            'status_id'            => (int)$expense->status_id,
            'recurring_start_date' => $expense->recurring_start_date ?: '',
            'recurring_end_date'   => $expense->recurring_end_date ?: '',
            'recurring_due_date'   => $expense->recurring_due_date ?: '',
            'last_sent_date'       => $expense->last_sent_date ?: '',
            'next_send_date'       => $expense->next_send_date ?: '',
            'recurring_frequency'  => (string)$expense->recurring_frequency ?: '',
            'category'             => $expense->category,
            'tax_2'                => (float)$expense->tax_2,
            'tax_3'                => (float)$expense->tax_3,
            'tax_rate_name_2'      => $expense->tax_rate_name_2 ?: '',
            'tax_rate_name_3'      => $expense->tax_rate_name_3 ?: '',
        ];
    }

    /**
     * @param $files
     * @return array
     */
    private function transformExpenseFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        return $files->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }

}
