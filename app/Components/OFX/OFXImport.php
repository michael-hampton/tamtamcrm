<?php


namespace App\Components\OFX;


use App\Factory\CompanyFactory;
use App\Factory\ExpenseFactory;
use App\Models\Account;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Repositories\ExpenseRepository;
use App\Transformations\OfxImportTransformable;
use OfxParser\Entities\Transaction;
use OfxParser\Parser;

class OFXImport
{
    use OfxImportTransformable;

    private array $companies = [];

    private array $expenses = [];

    public function __construct()
    {
        $this->companies = array_change_key_case(Company::get()->keyBy('name')->toArray(), CASE_LOWER);
        $this->expenses = Expense::whereNotNull('transaction_id')->get()->keyBy('transaction_id')->toArray();
    }

    /**
     * @param User $user
     * @param Account $account
     * @param ExpenseRepository $expense_repo
     * @param CompanyRepository $company_repo
     * @param array $transactions
     * @param array $selected_transactions
     * @param null $bank_id
     * @return array
     */
    public function import(
        User $user,
        Account $account,
        ExpenseRepository $expense_repo,
        CompanyRepository $company_repo,
        array $transactions,
        array $selected_transactions,
        $bank_id = null
    ) {
        $expenses_created = [];

        foreach ($transactions as $transaction) {
            if (!in_array($transaction['uniqueId'], $selected_transactions)) {
                continue;
            }

            if (array_key_exists($transaction['uniqueId'], $this->expenses)) {
                continue;
            }

            if (array_key_exists($transaction['name'], $this->companies)) {
                $company = $this->companies[$transaction['name']]['id'];
            } else {
                $company = (new CompanyFactory())->create($user, $account);
                $company = $company_repo->save(['name' => $transaction['name']], $company);
            }

            $expense = ExpenseFactory::create($user, $account);
            $data = [
                'create_invoice' => true,
                'exchange_rate'  => 1,
                'amount'         => $transaction['amount'],
                'transaction_id' => $transaction['uniqueId'],
                'company_id'     => $company->id,
                'public_notes'   => $transaction['memo'],
                'date'           => $transaction['date'],
                'bank_id'        => $bank_id
            ];

            $expense = $expense_repo->save($data, $expense);
            $expenses_created[] = $expense;
        }

        return $expenses_created;
    }

    public function preview($file)
    {
        $transactions = collect($this->buildImport($file, true))->where('amount', '<', 0);

        $transactions = $transactions->filter(
            function ($transaction, $key) {
                return !array_key_exists($transaction->uniqueId, $this->expenses);
            }
        );

        if (empty($transactions)) {
            return [];
        }

        $transactions = $transactions->map(
            function (Transaction $transaction) {
                return $this->transform($transaction);
            }
        )->all();

        return array_values($transactions);
    }

    private function buildImport($file, $is_preview = false)
    {
        $ofxParser = new Parser();
        $ofx = $ofxParser->loadFromFile($file);

        $bankAccount = reset($ofx->bankAccounts);

// Get the statement start and end dates
        $startDate = $bankAccount->statement->startDate;
        $endDate = $bankAccount->statement->endDate;

// Get the statement transactions for the account
        $transactions = $bankAccount->statement->transactions;

        if ($is_preview) {
            return $transactions;
        }
    }
}